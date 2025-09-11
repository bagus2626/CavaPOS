<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia; // Add this line
use Spatie\MediaLibrary\HasMedia; // Also implement this interface

class Portfolio extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['title', 'description', 'image', 'link', 'category', 'created_by', 'updated_by'];
}
