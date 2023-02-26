<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function approval_director()
    {
        return view('approval.director');
    }
}
