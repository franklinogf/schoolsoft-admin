<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'enviroments' => 'array',
    ];
}
