<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body class="login-register-body">

    <div class="container login-register-container"> 
        <div class="card login-register-card"> 
            <div class="card-title">
                <img src="{{ asset('storage/' . config('app.logo_path')) }}" alt="Logo" style="height: 80px;">
                <h2>Iniciar Sesión</h2>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Correo Electrónico" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
            <div class="footer-link">
                <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></p>
            </div>
        </div>
    </div>

</body>
</html>
