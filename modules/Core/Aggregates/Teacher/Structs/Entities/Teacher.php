<?php

namespace Core\Aggregates\Teacher\Structs\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Teacher extends Model
{
    protected $table = 'teachers';

    public function account(): MorphOne
    {
        return $this->morphOne(User::class, 'accountable');
    }
}
