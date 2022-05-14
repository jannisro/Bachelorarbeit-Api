<?php

namespace App\Models\Electricity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstalledCapacity extends Model
{
    use HasFactory;

    protected $table = "electricity_installed_capacity";
}
