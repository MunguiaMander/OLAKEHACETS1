<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #4F5B93;">
        <a class="navbar-brand text-white" href="#">Eventos Disponibles</a>
        <img src="{{ asset('storage/' . config('app.logo_path')) }}" alt="Logo de la página" class="img-fluid" style="max-height: 80px;">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Usuario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/logout">Cerrar Sesion</a>
                </li>
            </ul>
        </div>
    </nav>

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
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $event->post->title }}</h5>
                                <p class="card-text">{{ $event->post->description }}</p>
                                <p class="card-text">Fecha: {{ $event->event_date }} Hora: {{ $event->event_time }}</p>
                                <p class="card-text">Lugar: {{ $event->location }}</p>
                                <img src="{{ asset('storage/' . $event->image_path) }}" alt="Imagen del evento" class="img-fluid mb-3">
                                <button class="btn btn-danger" data-toggle="modal" data-target="#reportModal" data-post-id="{{ $event->post_id }}">Reportar</button>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Reportar Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                        <button type="submit" class="btn btn-danger">Enviar Reporte</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    @vite('resources/js/app.js')
</body>
</html>
