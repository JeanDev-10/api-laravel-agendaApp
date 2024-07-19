<?php

namespace App\Repository\Favorite;

use App\Interfaces\Favorite\FavoriteInterfaceRepository;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class FavoriteRepository implements FavoriteInterfaceRepository
{
    public function index(array $filters = [])
    {
        $user_id = auth()->user()->id;

        $query = Favorite::where('user_id', $user_id)
            ->with('Contact');

        // Aplicar filtros a través de whereHas
        if (isset($filters['name'])) {
            $query->whereHas('Contact', function ($subQuery) use ($filters) {
                $subQuery->where('name', 'like', '%' . $filters['name'] . '%');
            });
        }

        if (isset($filters['phone'])) {
            $query->whereHas('Contact', function ($subQuery) use ($filters) {
                $subQuery->where('phone', 'like', '%' . $filters['phone'] . '%');
            });
        }

        if (isset($filters['nickname'])) {
            $query->whereHas('Contact', function ($subQuery) use ($filters) {
                $subQuery->where('nickname', 'like', '%' . $filters['nickname'] . '%');
            });
        }

        // Ordenar por campos de la tabla relacionada solo si es necesario

        $favorites = $query->paginate(10);

        // Modificar cada favorito para agregar encrypted_id
        $favorites->getCollection()->transform(function ($favorite) {
            $favorite->encrypted_id = Crypt::encrypt($favorite->id);
            $favorite->contact->encrypted_id = Crypt::encrypt($favorite->contact->id);
            return $favorite;
        });

        return $favorites;
    }

    public function show($id)
    {
        $favorite = Favorite::with('Contact')->where('id', Crypt::decrypt($id))->first();
        $favorite->encrypted_id = Crypt::encrypt($favorite->id);
        $favorite->contact->encrypted_id = Crypt::encrypt($favorite->contact->id);
        return $favorite;
    }
    public function showById($id)
    {
        return Favorite::findOrFail(Crypt::decrypt($id));
    }
    public function store(Request $request)
    {
        $user_id = auth()->user()->id;
        Favorite::create([
            "user_id" => $user_id,
            "contact_id" => Crypt::decrypt($request->contact_id),
        ]);
    }
    public function delete(Favorite $favorite)
    {
        $favorite->delete();
    }
}
