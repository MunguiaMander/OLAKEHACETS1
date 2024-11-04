<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PublisherController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        switch ($user->role_id) {
            case 1: // Administrador
                return redirect()->route('dashboard');
            case 2: // Publicador
                return redirect()->route('publisher');
            case 3: // Registrado
                return redirect()->route('home');
            default:
                return redirect('/login');
        }
    }
    return redirect()->route('login');
});

// Rutas generales
Route::get('/home', [EventController::class, 'index'])->name('home');
Route::post('/search', [EventController::class, 'search'])->name('search.events');
Route::post('/report', [ReportController::class, 'reportEvent'])->name('report.event');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas del panel de administrador
Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
Route::post('/dashboard/unban/{id}', [AdminController::class, 'unbanPost'])->name('dashboard.unban');


// Rutas de eventos
Route::get('/events', [EventController::class, 'index'])->name('events');
Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
Route::post('/events/{id}/attend', [EventController::class, 'attendEvent'])->name('attend.event');


// Rutas del panel de publicador
Route::get('/publisher', [PublisherController::class, 'index'])->name('publisher');
Route::get('/publisher/event/create', [PublisherController::class, 'createEvent'])->name('publisher.create');
Route::post('/publisher/event/store', [PublisherController::class, 'storeEvent'])->name('publisher.store');
Route::get('/publisher/event/{eventId}/attendees', [PublisherController::class, 'getAttendees'])->name('publisher.getAttendees');
