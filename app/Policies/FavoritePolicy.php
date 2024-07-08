<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class FavoritePolicy
{
    public function show(User $user, $favorite): bool
    {
        return $user->id === $favorite->user_id;
    }
    public function store(User $user,   $contact_id): bool
    {
        return Contact::where('id', Crypt::decrypt($contact_id))->where('user_id', $user->id)->exists();
        /*  return $user->id === $favorite->user_id; */
    }

    public function delete(User $user,  Favorite $favorite): bool
    {
        return $user->id === $favorite->user_id;
    }
}
