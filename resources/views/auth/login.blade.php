<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Kopi Tempat Kamu Pulang (TKP) CRM</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-coffee: #2C1B18;
            --primary-coffee-light: #4A2E2B;
            --accent-gold: #C8963E;
            --bg-dark-espresso: #140C0B;
            --card-coffee-dark: #1F1412;
            --text-light: #F4EBE8;
            --text-muted-coffee: #A58E8A;
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

        .login-card {
            background-color: var(--card-coffee-dark);
            border: 1px solid rgba(200, 150, 62, 0.15);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }

        .login-header {
            text-align: center;
            padding: 2.5rem 2rem 1.5rem;
            border-bottom: 1px solid rgba(200, 150, 62, 0.1);
        }

        .login-header h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--accent-gold);
            margin: 0;
        }

        .login-header p {
            margin: 5px 0 0;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted-coffee);
        }

        .login-body {
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

        .demo-badge {
            background-color: rgba(200, 150, 62, 0.08);
            border: 1px dashed rgba(200, 150, 62, 0.25);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.82rem;
        }
        
        .demo-badge table td {
            padding: 2px 8px;
            color: var(--text-light);
        }
    </style>
</head>
<body>

    <div class="login-card animate-fade-in">
        <!-- Logo Header -->
        <div class="login-header">
            <h2>Kopi Tempat Kamu Pulang</h2>
            <p>Portal CRM & Loyalitas Member</p>
        </div>

        <div class="login-body">
            <!-- Session Status Alerts -->
            @if (session('status'))
                <div class="alert alert-success bg-success bg-opacity-10 text-success border-0 mb-3" style="font-size: 0.85rem;">
                    {{ session('status') }}
                </div>
            @endif

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

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-3">
                    <label for="email" class="form-label" style="color: var(--text-muted-coffee); font-weight: 500;">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted-coffee" style="border-color: rgba(200, 150, 62, 0.2) !important;"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" class="form-control form-control-coffee" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="username">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <label for="password" class="form-label" style="color: var(--text-muted-coffee); font-weight: 500;">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted-coffee" style="border-color: rgba(200, 150, 62, 0.2) !important;"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control form-control-coffee" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                        <label class="form-check-label text-muted-coffee" for="remember_me" style="font-size: 0.85rem;">
                            Ingat Saya
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-warning text-decoration-none" style="font-size: 0.85rem;">
                            Lupa Password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-gold w-100 py-2.5 text-uppercase">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk ke Dashboard
                </button>
            </form>

            <div class="text-center mt-3" style="font-size: 0.85rem;">
                <span class="text-muted-coffee">Belum punya akun member?</span>
                <a href="{{ route('register') }}" class="text-warning text-decoration-none fw-bold">Daftar Sekarang</a>
            </div>

            <!-- Demo Account Credentials helper -->
            <div class="demo-badge">
                <div class="text-warning fw-bold mb-1" style="font-size: 0.85rem;"><i class="fa-solid fa-circle-info me-1"></i> Akun Uji Coba Sidang (Password: <span class="text-light font-monospace">password</span>):</div>
                <table style="width: 100%;">
                    <tr>
                        <td class="text-muted-coffee">Owner:</td>
                        <td class="font-monospace fw-bold" style="font-size: 0.8rem;">owner@tkp.com</td>
                    </tr>
                    <tr>
                        <td class="text-muted-coffee">Barista:</td>
                        <td class="font-monospace fw-bold" style="font-size: 0.8rem;">barista@tkp.com</td>
                    </tr>
                    <tr>
                        <td class="text-muted-coffee">Member:</td>
                        <td class="font-monospace fw-bold" style="font-size: 0.8rem;">samuel@tkp.com</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
