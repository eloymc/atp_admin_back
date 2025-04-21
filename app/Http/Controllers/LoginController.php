<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioModel;
use App\Http\Controllers\PasswordHash;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    public $usuario;
    public $token;
    
    public function Login(Request $request){
        
        try {
            $valida = $request->validate([
                'usuario' => 'required|numeric',
                'password' => 'required' 
            ]);

            $this->ObtenerUsuario($request->usuario);
            if($this->usuario->count() > 0){
                $cifrado = $this::CrearHash($request->password,$this->usuario->salt);
                $validacion_pass = $this::ValidarPass($cifrado,$this->usuario->pasword);
                if($validacion_pass){
                    $this::GenerarToken($request->usuario);
                    $token = $this->usuario->createToken($request->usuario)->plainTextToken;
                    $datos = array("errors"=>null,"message"=>$token, "codigo"=>200);
                }else{
                    $datos = array("errors"=>"fail authentication","message"=>"Problemas para autenticar B", "codigo"=>403);
                }
            }else{
                $datos = array("errors"=>"fail authentication","message"=>"Problemas para autenticar A", "codigo"=>403);
            }
            
            return response()->json($datos,$datos['codigo'], [], JSON_UNESCAPED_UNICODE);
            
        }catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validacion',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            $token = $request->bearerToken();
            if(!is_null($token)){
                return response()->json([
                    'message' => 'El token es erroneo o ya expiro',
                    'error' => $e->getMessage(),
                ], 401);
            }else{
                
                return response()->json([
                    'message' => 'Ocurrio un error inesperado',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    }

    public function Usuario(Request $request){
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Token no proporcionado'], 401);
        }

        $user = $this->DatosToken($token);

        return response()->json($user,200);
    }

    public function DatosToken($token,$restringido = true){
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Token inv�lido'], 401);
        }

        $user = $accessToken->tokenable;
        if($restringido){
            return $user->makeHidden(['pasword', 'session_id', 'salt','fecha_ultimo_cambio_pass','password_nextel','ultimo_acceso','update_sesion','inicio_sesion','fecha_modificacion','ip_mobile','ip','fecha_ultimo_aviso']);
        }else{
            return $user;
        }
    }

    private function ObtenerUsuario($user){                
        $this->usuario = UsuarioModel::activo()->where('cveusuario',$user)->first();
    }

    private static function CrearHash($password,$salt){                
        $passwordHash = new PasswordHash();                
        $cifrada=base64_encode($passwordHash->pbkdf2("SHA256",$password, $salt, PBKDF2_ITERATIONS,PBKDF2_HASH_BYTE_SIZE));                  
        return $cifrada;
    }

    private static function ValidarPass($pass1,$pass2){
        $compare = (strcmp($pass1,$pass2) === 0) ? true : false;
        return $compare;
    }

    private static function GenerarToken(){
        
    }
}
