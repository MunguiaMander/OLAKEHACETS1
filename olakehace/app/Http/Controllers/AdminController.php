<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Event;

class AdminController extends Controller
{
    public function index()
    {
        // Verificar autenticación y rol
        $user_id = session('user_id');
        $user_role = session('user_role');
        if (!$user_id || $user_role != 1) {
            \Log::warning('Acceso denegado: Usuario no autenticado o no es administrador', ['user_id' => $user_id, 'user_role' => $user_role]);
            return redirect()->route('login')->with('error', 'Acceso denegado.');
        }
    
        // Obtener eventos con estado del post
        $bannedEvents = Event::whereHas('post', function ($query) {
            $query->where('status_id', 3); // 3 = Baneado
        })->get();
    
        $approvedEvents = Event::whereHas('post', function ($query) {
            $query->where('status_id', 1); // 1 = Aprobado
        })->get();
    
        return view('dashboard', compact('bannedEvents', 'approvedEvents'))->with('user_name', session('user_name'));
    }

    public function unbanPost($id)
    {
        $event = Event::find($id);
        if (!$event) {
            Log::warning('Evento no encontrado', ['event_id' => $id]);
            return redirect()->route('dashboard')->with('error', 'Evento no encontrado.');
        }
        $event->status_id = 1;
        $event->save();
        $post = $event->post;
        
        if (!$post) {
            Log::warning('Post asociado al evento no encontrado', ['event_id' => $id]);
            return redirect()->route('dashboard')->with('error', 'Publicación asociada no encontrada.');
        }
        $post->status_id = 1;
        $post->save();
    
        Log::info('Publicación y evento desbaneados exitosamente', ['post_id' => $post->id, 'event_id' => $id]);
    
        return redirect()->route('dashboard')->with('success', 'Publicación y evento desbaneados con éxito.');
    }
    
}