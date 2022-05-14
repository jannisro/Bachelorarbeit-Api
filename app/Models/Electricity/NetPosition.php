<?php

namespace App\Models\Electricity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetPosition extends Model
{
    use HasFactory;

    protected $table = "electricity_net_positions";
}
