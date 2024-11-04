<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Publicador</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/publisher.js')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-info">
        <a class="navbar-brand text-white" href="#">
            <img src="{{ asset('storage/site-images/olakehacelogo.png') }}" alt="Logo" style="max-height: 50px; margin-right: 10px;">
            Panel de Publicador
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
        <h4>Tus Eventos Creados</h4>
        <div id="eventsContainer" class="row">
            @foreach($events as $event)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <!-- Mostrar imagen del evento -->
                        @if($event->image_path)
                            <img src="{{ asset('storage/' . $event->image_path) }}" class="card-img-top" alt="Imagen del evento" style="max-height: 200px; object-fit: cover;">
                        @else
                            <img src="{{ asset('images/default-event.jpg') }}" class="card-img-top" alt="Imagen por defecto del evento" style="max-height: 200px; object-fit: cover;">
                        @endif

                        <div class="card-body">
                            <h5 class="card-title">{{ $event->post->title }}</h5>
                            <p class="card-text">{{ $event->post->description }}</p>
                            <p class="card-text">Fecha: {{ $event->event_date }} Hora: {{ $event->event_time }}</p>
                            <p class="card-text">Lugar: {{ $event->location }}</p>
                            
                            <!-- Mostrar estado del evento -->
                            <p class="card-text">
                                Estado: 
                                @if($event->status_id == 1)
                                    <span class="text-success">Aprobado</span>
                                @elseif($event->status_id == 2)
                                    <span class="text-warning">En revisión</span>
                                @elseif($event->status_id == 3)
                                    <span class="text-danger">Baneado</span>
                                @else
                                    <span class="text-secondary">Desconocido</span>
                                @endif
                            </p>
                            <button class="btn btn-outline-info btn-sm" onclick="showAttendeesModal({{ $event->id }})">
                                <i class="bi bi-people-fill"></i> Ver Asistentes
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>



    <!-- Botón flotante para crear nuevo evento -->
    <button id="createEventBtn" class="btn btn-info rounded-circle shadow-lg" style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px;" data-toggle="modal" data-target="#eventModal">
        <i class="bi bi-upload"></i>
    </button>

    <!-- Modal para crear o editar eventos -->
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="eventModalLabel">Crear Evento</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="eventForm" action="{{ route('publisher.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="title">Título</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="event_date">Fecha del Evento</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" required>
                        </div>
                        <div class="form-group">
                            <label for="event_time">Hora del Evento</label>
                            <input type="time" class="form-control" id="event_time" name="event_time" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Ubicación</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="capacity">Capacidad</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>
                        <div class="form-group">
                            <label for="audience_type">Tipo de Audiencia</label>
                            <input type="text" class="form-control" id="audience_type" name="audience_type" required>
                        </div>
                        <div class="form-group">
                            <label for="image">Imagen del Evento</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-success">Guardar Evento</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Asistentes -->
    <div class="modal fade" id="attendeesModal" tabindex="-1" aria-labelledby="attendeesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="attendeesModalLabel">Asistentes al Evento</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="attendeesList" class="list-group">
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Scripts de Bootstrap y Vite para JavaScript dinámico -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @vite('resources/js/eventStats.js')
</body>
</html>
