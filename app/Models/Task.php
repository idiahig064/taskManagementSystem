<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public static function rules()
    {
        return [
            'title' => 'required|string|min:5|max:100',
            'description' => 'nullable|string|max:500',
            'due_date' => 'required|date',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'category_id' => 'required|integer',
            'file_attachment' => 'nullable|file|max:5120', // 5MB in kilobytes
        ];
    }

    public static function rulesCreate()
    {
        return array_merge(self::rules(), [
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'category_id' => 'required|integer|min:1',
        ]);
    }

    public static function rulesUpdate()
    {
        return array_merge(self::rules(), [
            'status' => 'required|in:Completed,Incomplete',
        ]);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'task_id');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function history()
    {
        return $this->hasMany(TaskHistory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



}
