<?php
namespace App\Http\Repository\Auth;
use App\Http\Interfaces\Auth\AuthInterface;
use App\Http\Responses\ApiResponses;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthRepository implements AuthInterface{
    public function login(Request $request){
        $user = User::where("email", "=", $request->email)->first();

        if( isset($user->id) ){
            if(Hash::check($request->password, $user->password)){
                //creamos el token
                $token = $user->createToken("auth_token")->plainTextToken;
                //si está todo ok
                return ApiResponses::succes("Usuario logeado exitosamente", 200,$token);
            }else{
                return ApiResponses::error("Credenciales incorrectas",404);
            }
        }else{
            return ApiResponses::error("Usuario no registrado",404);

        }
    }
    public function register(Request $request){
        User::create([
            "firstname"=>$request->firstname,
            "lastname"=>$request->lastname,
            "email"=>$request->email,
            "password"=>Hash::make($request->password),
        ]);
        return ApiResponses::succes("Usuario creado correctamente",201);

    }
    public function userProfile(){
        $user = Auth::guard('sanctum')->user();
        return ApiResponses::succes("Perfil de usuario",200,$user);
    }
    public function logout(){
        $user = Auth::guard('sanctum')->user();
        $user->tokens()->delete();
        return ApiResponses::succes("Cierre de sesión exitoso",200);
    }
}
