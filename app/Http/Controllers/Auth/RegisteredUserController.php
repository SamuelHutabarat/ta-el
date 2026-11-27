<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\User;
use App\Support\Telepon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Pendaftaran member: cukup nama lengkap dan nomor telepon (tanpa email & alamat).
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['no_telp' => Telepon::normalisasi($request->input('no_telp'))]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'string', 'max:20', 'unique:'.User::class.',no_telp'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => null,
            'no_telp' => $request->no_telp,
            'password' => Hash::make($request->password),
            'role' => 'pelanggan',
        ]);

        // Profil member: tautkan ke data offline jika nomornya sudah pernah didaftarkan kasir
        Pelanggan::buatUntukAkun($user);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
