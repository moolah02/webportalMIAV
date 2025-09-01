<?php

// app/Models/ReportDefinition.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportDefinition extends Model
{
    protected $fillable = ['name','owner_id','visibility','definition_json'];
    protected $casts = ['definition_json' => 'array'];
}
