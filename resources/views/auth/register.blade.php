<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Member - Kopi Tempat Kamu Pulang (TKP)</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-coffee: #3A2420;
            --primary-coffee-light: #563732;
            --accent-gold: #D4A047;
            --accent-gold-hover: #EAB965;
            --bg-dark-espresso: #221614;
            --card-coffee-dark: #2D1E1B;
            --text-light: #FAF5F2;
            --text-muted-coffee: #C7B3AE;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at center, var(--primary-coffee-light) 0%, var(--bg-dark-espresso) 100%);
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .register-card {
            background-color: var(--card-coffee-dark);
            border: 1px solid rgba(212, 160, 71, 0.22);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
        }

        .register-header {
            text-align: center;
            padding: 2.5rem 2rem 1.5rem;
            border-bottom: 1px solid rgba(200, 150, 62, 0.1);
        }

        .register-header h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--accent-gold);
            margin: 0;
        }

        .register-header p {
            margin: 5px 0 0;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted-coffee);
        }

        .register-body {
            padding: 2rem;
        }

        .form-control-coffee {
            background-color: rgba(0, 0, 0, 0.25) !important;
            border: 1px solid rgba(200, 150, 62, 0.2) !important;
            color: var(--text-light) !important;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }

        .form-control-coffee:focus {
            background-color: rgba(0, 0, 0, 0.35) !important;
            border-color: var(--accent-gold) !important;
            box-shadow: 0 0 0 0.25rem rgba(200, 150, 62, 0.15) !important;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--accent-gold), #B58231);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.75rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(200, 150, 62, 0.2);
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #E5B25D, var(--accent-gold));
            color: #fff;
            box-shadow: 0 6px 14px rgba(200, 150, 62, 0.3);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="register-card">
        <!-- Logo Header -->
        <div class="register-header">
            <h2>Kopi Tempat Kamu Pulang</h2>
            <p>Daftar Member Baru (nama &amp; nomor telepon)</p>
        </div>

        <div class="register-body">
            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger bg-danger bg-opacity-10 text-danger border-0 mb-3" style="font-size: 0.85rem;">
                    <ul class="m-0 p-0" style="list-style: none;">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-xmark me-1"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name Input -->
                <div class="mb-3">
                    <label for="name" class="form-label" style="color: var(--text-muted-coffee); font-weight: 500;">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted-coffee" style="border-color: rgba(200, 150, 62, 0.2) !important;"><i class="fa-solid fa-user"></i></span>
                        <input type="text" class="form-control form-control-coffee" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Rian Pratama" required autofocus autocomplete="name">
                    </div>
                </div>

                <!-- Phone Input -->
                <div class="mb-3">
                    <label for="no_telp" class="form-label" style="color: var(--text-muted-coffee); font-weight: 500;">Nomor Telepon (untuk login)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted-coffee" style="border-color: rgba(200, 150, 62, 0.2) !important;"><i class="fa-solid fa-phone"></i></span>
                        <input type="tel" class="form-control form-control-coffee" id="no_telp" name="no_telp" value="{{ old('no_telp') }}" placeholder="Contoh: 081234567890" required autocomplete="tel">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <label for="password" class="form-label" style="color: var(--text-muted-coffee); font-weight: 500;">Kata Sandi (Password)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted-coffee" style="border-color: rgba(200, 150, 62, 0.2) !important;"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control form-control-coffee" id="password" name="password" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                    </div>
                </div>

                <!-- Confirm Password Input -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label" style="color: var(--text-muted-coffee); font-weight: 500;">Konfirmasi Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted-coffee" style="border-color: rgba(200, 150, 62, 0.2) !important;"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control form-control-coffee" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi" required autocomplete="new-password">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-gold w-100 py-2.5 text-uppercase">
                    <i class="fa-solid fa-user-plus me-2"></i> Daftarkan Keanggotaan
                </button>
            </form>

            <div class="text-center mt-3" style="font-size: 0.85rem;">
                <span class="text-muted-coffee">Sudah punya akun member?</span>
                <a href="{{ route('login') }}" class="text-warning text-decoration-none fw-bold">Masuk Sekarang</a>
            </div>
        </div>
    </div>

</body>
</html>
