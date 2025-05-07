<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function isParent(): bool
    {
        return $this->children()->exists();
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function tasks()
    {
        return $this->hasMany(\App\Models\Task::class);
    }

}
