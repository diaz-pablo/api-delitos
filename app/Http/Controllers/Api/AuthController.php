<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validaciones previas
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Si falla la validación, devolver error 422
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación de datos',
                'data' => null,
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $email = strtolower($request->get('email'));

            // Obtener usuario desde BD
            $user = User::query()
                    ->where('email', $email)
                    ->first();

            // Verificar existencia del usuario
            if ($user === NULL) {
                return response()->json([
                    'status' => false,
                    'message' => 'Credenciales incorrectas',
                    'data' => null,
                    'errors' => [
                        'auth' => ['Las credenciales proporcionadas no son correctas.']
                    ]
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Validación del el email y la contraseña
            $credentials = [
                'email' => $email,
                'password' => $request->get('password')
            ];  

            // Credenciales incorrectas
            if (! Auth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Credenciales incorrectas',
                    'data' => null,
                    'errors' => [
                        'auth' => ['Las credenciales proporcionadas no son correctas.']
                    ]
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Credenciales correctas

            // Eliminamos todos los tokens anteriores generados por el usuario
            auth()->user()->tokens()->delete(); // Opcional

            // Generamos un nuevo token
            $token = auth()->user()->createToken($email, []);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => array(
                    'user_id' => $user->id,
                    'user_name' => $user->name, 
                    'user_email' => $email,
                    'token' => $token->plainTextToken,
                    'token_type' => config('sanctum.token_type'), 
                    'token_expiration' => config('sanctum.expiration'),
                ),
                'errors' => null
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Error interno del servidor',
                // 'message' => $e->getMessage(),
                'data' => null,
                'errors' => null 
            ], Response::HTTP_INTERNAL_SERVER_ERROR);          
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Error interno del servidor', 
                // 'message' => $th->getMessage(),
                'data' => null,
                'errors' => null 
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } 
    }
}
