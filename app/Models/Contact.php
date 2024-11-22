<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class Contact extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps=false;
    public $table="contacts";
    protected $fillable = [
        'name',
        'phone',
        'nickname',
        'user_id',
    ];
    protected $hidden = ['user_id', 'deleted_at'];
    public function User():BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function Favoritos():HasOne{
        return $this->hasOne(Favorite::class);
    }
}
