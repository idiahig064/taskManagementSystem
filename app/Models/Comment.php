<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $table = 'task_comments'; // Specify the table name
    protected $fillable = ['task_id', 'user_id', 'comment'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
