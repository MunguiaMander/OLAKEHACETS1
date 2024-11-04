<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        // Verifica si el usuario está autenticado
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Por favor, inicia sesión primero.');
        }
    
        $user = [
            'id' => session('user_id'),
            'role_id' => session('user_role'),
            'name' => session('user_name')
        ];
    
        $events = Event::with('post')
                        ->where('status_id', 1)
                        ->where('status_id', '!=', 3)
                        ->whereDate('event_date', '>=', now())
                        ->get();
    
        return view('home', ['user' => $user, 'events' => $events]);
    }    

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $events = Event::with('post')
                    ->where('status_id', 1) 
                    ->where('status_id', '!=', 3) 
                    ->whereHas('post', function ($q) use ($query) {
                        $q->where('title', 'LIKE', "%$query%");
                    })
                    ->whereDate('event_date', '>=', now()) 
                    ->get();
        
        return view('home', compact('events'));
    }

    public function store(Request $request)
    {
        // Solo permite que el Publicador pueda acceder a esta ruta
        if (!Auth::check() || Auth::user()->role_id != 2) {
            return redirect()->route('home')->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'location' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('eventsimg'), $fileName);
            $imagePath = 'eventsimg/' . $fileName;
        } else {
            $imagePath = null;
        }

        $event = new Event();
        $event->post_id = $request->post_id;
        $event->event_date = $request->event_date;
        $event->event_time = $request->event_time;
        $event->location = $request->location;
        $event->image_path = $imagePath;
        $event->save();

        return redirect()->back()->with('success', 'Evento creado con éxito.');
    }

    public function attendEvent($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Por favor, inicia sesión primero.');
        }
    
        $user_id = session('user_id');
    
        // Comprobar si el usuario ya está registrado como asistente a este evento
        $alreadyAttending = \DB::table('attendances')
                            ->where('user_id', $user_id)
                            ->where('event_id', $id)
                            ->exists();
    
        if ($alreadyAttending) {
            // Redirigir con un mensaje de aviso
            return redirect()->back()->with('info', 'Ya estás registrado para asistir a este evento.');
        }
    
        // Registrar la asistencia
        \DB::table('attendances')->insert([
            'user_id' => $user_id,
            'event_id' => $id
        ]);
    
        // Redirigir con un mensaje de éxito
        return redirect()->back()->with('success', 'Te has registrado para asistir al evento con éxito.');
    }
    
    


}

