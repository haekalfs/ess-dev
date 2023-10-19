<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor_list extends Model
{
    protected $connection = 'eform';

    protected $table = 'vendor_list';
    protected $fillable = ['id', 'vendor_type','company','contact_name', 'email_address', 'phone_number', 'address', 'country'];
}
