<?php
namespace App\Repository\Auth;
use App\Interfaces\Auth\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\ApiResponses;

class AuthRepository implements AuthInterface{
    public function login(Request $request){
        $user = User::where("email", "=", $request->email)->first();

        if( isset($user->id) ){
            if(Hash::check($request->password, $user->password)){
                //creamos el token
                $token = $user->createToken("auth_token")->plainTextToken;
                return ApiResponses::successs("Usuario logeado exitosamente", 200, $token);
            }else{
                return ApiResponses::error("Credenciales incorrectas",404,["message"=>"Credenciales incorrectas"]);
            }
        }else{
            return ApiResponses::error("Usuario no registrado",404,["message"=>"Usuario no registrado"]);
        }

    }
    public function register(Request $request){
        User::create([
            "firstname"=>$request->firstname,
            "lastname"=>$request->lastname,
            "email"=>$request->email,
            "password"=>Hash::make($request->password),
        ]);
    }
    public function userProfile(){
        $user = Auth::guard('sanctum')->user();
        return $user;
    }
    public function logout(){
        $user = Auth::guard('sanctum')->user();
        $user->tokens()->delete();
    }
}
