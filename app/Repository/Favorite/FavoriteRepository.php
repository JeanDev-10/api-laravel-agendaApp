<?php
namespace App\Repository\Favorite;

use App\Interfaces\Favorite\FavoriteInterfaceRepository;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class FavoriteRepository implements FavoriteInterfaceRepository
{
    public function index(){
        $user_id=auth()->user()->id;
        $favorites=Favorite::with('Contact')->where('user_id',$user_id)->get();
        foreach ($favorites as $favorite) {
            $favorite->encrypted_id = Crypt::encrypt($favorite->id);
            $favorite->contact->encrypted_id = Crypt::encrypt($favorite->contact->id);
        }
        return $favorites;
    }
    public function show($id){
        $favorite=Favorite::with('Contact')->where('id',Crypt::decrypt($id))->first();
        $favorite->encrypted_id= Crypt::encrypt($favorite->id);
        $favorite->contact->encrypted_id = Crypt::encrypt($favorite->contact->id);
        return $favorite;
    }
    public function showById($id){
        return Favorite::findOrFail(Crypt::decrypt($id));
    }
    public function store(Request $request){
        $user_id=auth()->user()->id;
        Favorite::create([
            "user_id"=> $user_id,
            "contact_id"=> Crypt::decrypt($request->contact_id),
        ]);
    }
    public function delete(Favorite $favorite){
        $favorite->delete();
    }
}
