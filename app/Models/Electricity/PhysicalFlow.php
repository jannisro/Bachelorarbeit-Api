<?php

namespace App\Models\Electricity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalFlow extends Model
{
    use HasFactory;

    protected $table = "electricity_flow_physical";
}
