<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'thumbnail',
    ];

    protected $appends = ['thumbnail_url']; // Tự động thêm khi trả về JSON

    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail) {
            return null; // Nếu không có ảnh, trả về null
        }

        return asset('storage/' . $this->thumbnail); // Đảm bảo chỉ có 1 lần asset()
    }
}