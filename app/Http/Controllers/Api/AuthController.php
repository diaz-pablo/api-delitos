<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6', 
        ]);

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
            $password = $request->get('password');

            // Verificamos credenciales usando el guard 'web', ya que
            // Sanctum no proporciona un método attempt(), porque su propósito no es validar credenciales, sino gestionar tokens personales.
            if (! Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Credenciales incorrectas',
                    'data' => null,
                    'errors' => [
                        'auth' => ['Las credenciales proporcionadas no son correctas.']
                    ]
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Obtenemos el usuario autenticado desde el guard web
            $user = Auth::guard('web')->user();

            // Opcional: eliminamos todos los tokens anteriores
            $user->tokens()->delete();

            // Creamos un nuevo token
            $token = $user->createToken($email);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'token' => $token->plainTextToken,
                    'token_type' => config('sanctum.token_type', 'Bearer'),
                    'token_expiration' => config('sanctum.expiration'),
                ],
                'errors' => null
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error interno del servidor',
                'data' => null,
                'errors' => config('app.debug') ? ['exception' => $e->getMessage()] : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
