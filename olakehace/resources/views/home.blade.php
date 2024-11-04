<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</head>
<body 
    data-toast-message="{{ session('success') ?? session('info') ?? session('error') }}" 
    data-toast-type="{{ session('success') ? 'bg-success' : (session('info') ? 'bg-info' : (session('error') ? 'bg-danger' : '')) }}"
>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #4F5B93;">
        <a class="navbar-brand text-white" href="#">Eventos Disponibles</a>
        <img src="{{ asset('storage/' . config('app.logo_path')) }}" alt="Logo de la página" class="img-fluid" style="max-height: 80px;">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">
                        {{ session('user_name', 'Usuario') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/logout">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Bienvenida personalizada -->
    <section class="bg-light py-3">
        <div class="container">
            <h4 class="text-center">Bienvenido, {{ session('user_name') }}!</h4>
            <p class="text-center">Nos alegra que estés aquí. Explora los eventos disponibles a continuación.</p>
        </div>
    </section>

    <header class="bg-light py-3">
        <div class="container">
            <form action="{{ route('search.events') }}" method="POST" class="form-inline">
                @csrf
                <input type="text" name="query" class="form-control mr-sm-2" placeholder="Buscar eventos por nombre">
                <button type="submit" class="btn btn-dark">Buscar</button>
            </form>
        </div>
    </header>

    <main class="container mt-4">
        @if($events->isEmpty())
            <p>No hay eventos disponibles.</p>
        @else
            <div class="row">
                @foreach($events as $event)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <img src="{{ asset('storage/' . $event->image_path) }}" alt="Imagen del evento" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->post->title }}</h5>
                            <p class="card-text">{{ $event->post->description }}</p>
                            <p class="card-text"><strong>Fecha:</strong> {{ $event->event_date }} <strong>Hora:</strong> {{ $event->event_time }}</p>
                            <p class="card-text"><strong>Lugar:</strong> {{ $event->location }}</p>

                            <div class="d-flex justify-content-between">
                                <!-- Botón de Reportar -->
                                <button class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#reportModal" data-post-id="{{ $event->post_id }}">
                                    <i class="bi bi-flag-fill"></i> Reportar
                                </button>

                                <!-- Botón de Asistir -->
                                <form action="{{ route('attend.event', $event->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm" style="background-color: #4F5B93; border-color: #4F5B93;">
                                        <i class="bi bi-person-plus-fill"></i> Asistir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </main>

    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #4F5B93;">
                    <h5 class="modal-title text-white" id="reportModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Reportar Evento</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="reportForm" action="{{ route('report.event') }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_id" id="post_id">
                        <div class="form-group">
                            <label for="reason">Motivo del reporte</label>
                            <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Escribe el motivo del reporte"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-send-fill"></i> Enviar Reporte
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor de Notificación tipo Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        <div id="toastMessage" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @vite('resources/js/notifications.js')
    @vite('resources/js/app.js')
</body>
</html>
