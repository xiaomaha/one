<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class User extends Model
{
    //
    protected $table = 'admin_users';
}
