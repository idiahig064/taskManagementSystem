<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskHistory extends Model
{
    use SoftDeletes;

    protected $table = 'task_history';

    protected $fillable = ['task_id', 'user_id', 'action', 'old_value', 'new_value'];
}
