<?php

namespace Core\Aggregates\Subject\Structs\Entities;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = ['name'];
}
