<?php

namespace App\Http\Controllers;

use App\Models\Commands;
use Illuminate\Http\Request;

class CommandsController extends Controller
{
    public function index()
    {
        $data = Commands::all();
        return view('commands.index', ['data' => $data]);
    }
}
