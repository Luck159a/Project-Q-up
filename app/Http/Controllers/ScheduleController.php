<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display the queue booking schedule
     */
    public function index()
    {
        return view('queue.schedule');
    }
}
