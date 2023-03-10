<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\Medical;

class MedicalController extends Controller
{
    public function index()
    {
        $med = Medical::all();
        return view('medical.medical',['med' => $med]);
        
    }
    public function entry()
    {
    	return view('medical.medical_tambah');
    }
}