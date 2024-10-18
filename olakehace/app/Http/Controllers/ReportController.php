<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function reportEvent(Request $request)
    {
        Report::create([
            'user_id' => 1,
            'post_id' => $request->post_id,
            'reason' => $request->reason
        ]);

        return redirect()->back()->with('success', 'Evento reportado con éxito.');
    }
}
