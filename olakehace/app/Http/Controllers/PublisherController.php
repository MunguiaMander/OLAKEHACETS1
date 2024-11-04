<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PublisherController extends Controller
{
    public function index()
    {
        $user_id = session('user_id');
    
        if (!$user_id) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero.');
        }
    
        // Obtener los eventos creados por el usuario autenticado
        $events = Event::whereHas('post', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->get();
    
    
        return view('publisher', compact('events'))->with('user_name', session('user_name'));
    }
    
    
    public function getEvents()
    {
        $user_id = session('user_id');
        $events = Event::whereHas('post', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->get();

        return response()->json($events);
    }


    public function createEvent()
    {
        return view('publisher.create');
    }

    public function storeEvent(Request $request)
    {
        Log::info('Inicio de storeEvent');

        try {
            // Validación de datos
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'event_date' => 'required|date',
                'event_time' => 'required',
                'location' => 'required|string',
                'capacity' => 'required|integer',
                'audience_type' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            
            Log::info('Datos validados correctamente');
            
            // Continúa con la creación del post y el evento
            $post = Post::create([
                'user_id' => session('user_id'),
                'category_id' => 1, // Ajusta según sea necesario
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'status_id' => 1, // Publicado
            ]);

            Log::info('Publicación creada', ['post' => $post]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('event-images', 'public');
                Log::info('Imagen cargada', ['image_path' => $imagePath]);
            }

            $event = Event::create([
                'post_id' => $post->id,
                'event_date' => $request->input('event_date'),
                'event_time' => $request->input('event_time'),
                'location' => $request->input('location'),
                'capacity' => $request->input('capacity'),
                'audience_type' => $request->input('audience_type'),
                'image_path' => $imagePath,
            ]);

            Log::info('Evento creado', ['event' => $event]);

            return redirect()->route('publisher')->with('success', 'Evento creado con éxito.');

        } catch (ValidationException $e) {
            Log::error('Error de validación', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    public function getAttendees($eventId)
    {
        $user_id = session('user_id');

        // Verificar que el usuario autenticado sea el creador del evento
        $event = Event::where('id', $eventId)->whereHas('post', function($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->first();

        if (!$event) {
            return response()->json(['error' => 'No tienes permiso para ver los asistentes de este evento.'], 403);
        }

        // Obtener la lista de asistentes
        $attendees = \DB::table('attendances')
            ->join('app_users', 'attendances.user_id', '=', 'app_users.id')
            ->where('attendances.event_id', $eventId)
            ->select('app_users.name', 'app_users.email')
            ->get();

        return response()->json($attendees);
    }

    
}
