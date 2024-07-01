<?php
namespace App\Repository\User;
use App\Http\Responses\ApiResponses;
use App\Interfaces\User\UserInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UserRepository implements UserInterface{
    public function changePassword(Request $request){
        $user_id = auth()->user()->id;
        $user = User::where("id", "=", $user_id)->first();
            if(Hash::check($request->password, $user->password)){
                $encryptedPassword=Hash::make($request->new_password);
                if(Hash::check($request->password, $encryptedPassword)){
                  return ApiResponses::error("No hay cambios en las contraseñas", 422);
                }
                $user->password=$encryptedPassword;
                $user->save();
                return ApiResponses::successs("Contraseña actualizada exitosamente", 200);
            }else{
                return ApiResponses::error("Contraseña actual incorrecta",401,["message"=>"Contraseña incorrecta"]);
            }
    }
    public function CheckThePassword(Request $request){
        $user_id = auth()->user()->id;
        $user = User::where("id", "=", $user_id)->first();
            if(Hash::check($request->password, $user->password)){
                return ApiResponses::successs("Contraseña correcta!", 200);
            }else{
                return ApiResponses::error("Contraseña actual incorrecta",401,["message"=>"Contraseña incorrecta"]);
            }
    }
    public function editProfile(Request $request){
        $user_id = auth()->user()->id;
        $user = User::where("id", "=", $user_id)->first();
        if ($request->firstName === $user->firstname && $request->lastName === $user->lastname) {
            return ApiResponses::successs("No hay cambios, perfil no fue actualizado", 200,["message"=>"No hay cambios, perfil no fue actualizado"]);
        }
        $user->update([
            "firstname"=>$request->firstName,
            "lastname"=>$request->lastName,
        ]);
        return ApiResponses::successs("Perfil actualizado exitosamente", 200);
    }
}
