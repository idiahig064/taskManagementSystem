<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAttachment extends Model
{
    use SoftDeletes;

    protected $table = 'task_attachments';

    protected $fillable = ['task_id', 'file_name', 'file_path', 'file_size'];
}
