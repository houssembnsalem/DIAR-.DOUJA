<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - DIAR DOUJA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        }

        .login-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-brand .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 30px; color: white;
            margin: 0 auto 16px;
            box-shadow: 0 8px 20px rgba(37,99,235,0.3);
        }

        .login-brand h1 {
            font-size: 22px; font-weight: 700; color: #0f172a;
        }

        .login-brand p { color: #64748b; font-size: 14px; }

        .form-control {
            border-color: #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .form-label { font-weight: 500; font-size: 14px; color: #374151; }

        .btn-login {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 15px;
            width: 100%;
            color: white;
            transition: all 0.2s;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(37,99,235,0.4); }

        .demo-creds {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 14px;
            margin-top: 20px;
            font-size: 13px;
        }

        .input-group-text {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon"><i class="bi bi-house-heart-fill"></i></div>
            <h1>DIAR DOUJA</h1>
            <p>Connectez-vous à votre espace de gestion</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-sm mb-3">
                <i class="bi bi-exclamation-triangle me-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label" for="email">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="votre@email.com" required autofocus autocomplete="username">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                </div>
            </div>

            <div class="mb-4 d-flex align-items-center">
                <input type="checkbox" name="remember" id="remember" class="form-check-input me-2">
                <label for="remember" class="form-check-label" style="font-size:14px;">Se souvenir de moi</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
            </button>
        </form>

        <div class="demo-creds">
            <div class="fw-600 mb-1" style="font-weight:600; color:#0369a1;">
                <i class="bi bi-info-circle me-1"></i>Comptes de démonstration
            </div>
            <div><strong>Admin:</strong> admin@gestionlocation.com / password</div>
            <div><strong>Assistant:</strong> assistant@gestionlocation.com / password</div>
        </div>
    </div>

    <div class="text-center mt-4" style="color: rgba(255,255,255,0.4); font-size: 12px;">
        © {{ date('Y') }} DIAR DOUJA — Tous droits réservés
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
