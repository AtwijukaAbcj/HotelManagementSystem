<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'address', 'timezone', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function rooms()
    {
        return $this->hasMany(addrooms::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }
}
