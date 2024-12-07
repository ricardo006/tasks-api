<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';
    
    protected $fillable = ['title', 'description', 'status', 'user_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $task->id = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
