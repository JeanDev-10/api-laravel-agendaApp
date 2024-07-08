<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
        'contact_id',
        'user_id',
    ];

    public function User():BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function Contact():BelongsTo{
        return $this->belongsTo(Contact::class);
    }
}
