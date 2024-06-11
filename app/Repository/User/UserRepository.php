<?php
namespace App\Repository\User;
use App\Http\Responses\ApiResponses;
use App\Interfaces\User\UserInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class UserRepository implements UserInterface{
    public function changePassword(Request $request){
        $id = Auth::guard('sanctum')->user()->id;
        $user = User::where("id", "=", $id)->first();
            if(Hash::check($request->password, $user->password)){
                $user->password=Hash::make($request->new_password);
                $user->save();
                return ApiResponses::succes("Contraseña actualizada exitosamente", 200);
            }else{
                return ApiResponses::error("Contraseña actual incorrecta",401,["message"=>"Contraseña incorrecta"]);
            }
    }
}
