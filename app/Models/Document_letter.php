<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document_letter extends Model
{
    use HasFactory;

    protected $connection = 'eform';

    protected $table = 'doc_letter';
}
