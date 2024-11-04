<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/admin.js')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-dark">
        <a class="navbar-brand text-white" href="#">
            <img src="{{ asset('storage/site-images/olakehacelogo.png') }}" alt="Logo" style="max-height: 50px; margin-right: 10px;">
            Panel de Administrador
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Bienvenido, {{ session('user_name') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('logout') }}">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Panel de Publicaciones</h2>
        <div class="row">
            <!-- Publicaciones Baneadas -->
            <div class="col-md-6">
                <h4>Publicaciones Baneadas</h4>
                @foreach ($bannedEvents as $event)
                    <div class="card mb-3 shadow-sm">
                        @if ($event->image_path)
                            <img src="{{ asset('storage/' . $event->image_path) }}" class="card-img-top" alt="{{ $event->post->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <img src="{{ asset('images/default-placeholder.png') }}" class="card-img-top" alt="Imagen no disponible" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->post->title }}</h5>
                            <p class="card-text">{{ $event->post->description }}</p>
                            <form action="{{ route('dashboard.unban', $event->post->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Desbanear</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Publicaciones Aprobadas -->
            <div class="col-md-6">
                <h4>Publicaciones Aprobadas</h4>
                @foreach ($approvedEvents as $event)
                    <div class="card mb-3 shadow-sm">
                        @if ($event->image_path)
                            <img src="{{ asset('storage/' . $event->image_path) }}" class="card-img-top" alt="{{ $event->post->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <img src="{{ asset('images/default-placeholder.png') }}" class="card-img-top" alt="Imagen no disponible" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->post->title }}</h5>
                            <p class="card-text">{{ $event->post->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
