<?php

namespace App\Http\Controllers;

use App\Mail\EssMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailerController extends Controller
{
    public function index(){
 
		Mail::to("haekal@perdana.co.id")->send(new EssMailer());
 
		return "Email telah dikirim";
 
	}
}
