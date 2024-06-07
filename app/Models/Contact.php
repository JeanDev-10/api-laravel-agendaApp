<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;
    protected $timestamps=false;
    public $table="contacts";
    protected $fillable = [
        'name',
        'phone',
        'email',
        'nickname',
        'user_id',
    ];
    public function User():BelongsTo{
        return $this->belongsTo(User::class);
    }
}
