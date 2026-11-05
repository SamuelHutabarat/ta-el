<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pelanggan',
        ]);

        // Create associated Pelanggan profile
        $latestPelanggan = \App\Models\Pelanggan::orderBy('id', 'desc')->first();
        $nextNum = $latestPelanggan ? ((int)substr($latestPelanggan->kode_pelanggan, 4)) + 1 : 1;
        $kode = 'PLG-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $pelanggan = \App\Models\Pelanggan::create([
            'user_id' => $user->id,
            'kode_pelanggan' => $kode,
            'nama' => $user->name,
            'no_telp' => $request->no_telp ?? '-',
            'alamat' => $request->alamat ?? '-',
            'poin' => 0,
            'status_member' => 'Bronze',
        ]);

        // Create associated LoyalitasPelanggan record
        \App\Models\LoyalitasPelanggan::create([
            'pelanggan_id' => $pelanggan->id,
            'total_akumulasi_transaksi' => 0,
            'level_member' => 'Bronze',
            'diskon_khusus' => 0,
            'tanggal_bergabung' => \Carbon\Carbon::now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
