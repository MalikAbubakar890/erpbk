<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UploadFile extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'detail', 'path', 'uploaded_by'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUploadedAtAttribute()
    {
        return $this->created_at->format('d M Y, h:i A');
    }
}
