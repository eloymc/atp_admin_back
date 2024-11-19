<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioModel;

class LoginController extends Controller
{
    public function Login(Request $req){
        try {
            $valida = $req->validate([
                'usuario' => 'required|numeric',
                'password' => 'required' 
            ]);
            $usuario_model = UsuarioModel::activo()->where('cveusuario',$req->usuario);
            if($usuario_model->count() > 0){
                $usuario = $usuario_model->first();
                $cifrado = $this::create_hash($req->password,$usuario->salt);
                dd($cifrado);
            }
            
        }catch (ValidationException $e) {
            // Capturar y devolver los errores de validacion
            return response()->json([
                'message' => 'Errores de validacion',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejo de otras excepciones
            return response()->json([
                'message' => 'Ocurrio un error inesperado',
                'error' => $e->getMessage(),
            ], 500);
        }
        //dd($req->usuario);
        
    }

    private static function create_hash($password,$salt){                
      $cifrada=base64_encode(pbkdf2("SHA256",$password, $salt, PBKDF2_ITERATIONS,PBKDF2_HASH_BYTE_SIZE));                  
      return $cifrada;
    }
}
