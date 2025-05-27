<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class DelitoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $delitos = Delito::with(['user', 'tipo_delito'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Listado de Delitos',
            'data' => $delitos,
            'errors' => null
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        
        $validator = Validator::make($request->all(), [
            'tipo_delito_id' => 'required|exists:tipos_delitos,id',
            'fecha_ocurrencia' => 'required|date',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación de datos',
                'data' => null,
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Crear el delito con el user_id del token
        $data = $validator->validated();
        $data['user_id'] = auth()->user()->id;

        $delito = Delito::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Delito creado exitosamente',
            'data' => $delito,
            'errors' => null
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Delito $delito)
    {
        $delito->load(['user', 'tipo_delito']);

        return response()->json([
            'status' => true,
            'message' => 'Delito ' . $delito->id,
            'data' => $delito,
            'errors' => null
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delito $delito)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|exists:users,id',
            'tipo_delito_id' => 'sometimes|exists:tipos_delitos,id',
            'fecha_ocurrencia' => 'sometimes|date',
            'latitud' => 'sometimes|numeric',
            'longitud' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'data' => null,
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = auth()->user();

        // Opcional: verificar que el delito pertenezca al usuario autenticado
        if ($delito->user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'No autorizado para actualizar este delito',
                'data' => null,
                'errors' => ['auth' => ['No tenés permiso para modificar este recurso.']]
            ], Response::HTTP_FORBIDDEN);
        }

        $data = $validator->validated();
        $data['user_id'] = $user->id;

        $delito->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Delito actualizado',
            'data' => $delito->fresh()->load(['user', 'tipo_delito']),
            'errors' => null
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delito $delito)
    {
        $delito->delete();

        return response()->json([
            'status' => true,
            'message' => 'Delito eliminado correctamente',
            'data' => null,
            'errors' => null
        ], Response::HTTP_OK);
    }
}
