<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('post')
                    ->where('status_id', 1)  
                    ->where('status_id', '!=', 3)  
                    ->whereDate('event_date', '>=', now()) 
                    ->get();
        return view('home', compact('events'));
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
}
