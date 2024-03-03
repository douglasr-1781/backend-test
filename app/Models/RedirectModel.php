<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;

class RedirectModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'redirect';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url_to',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Hashids::encode($value)
        );
    }
    
    protected function active(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value == 1? true : false
        );
    }
}
