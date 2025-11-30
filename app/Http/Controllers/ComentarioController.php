<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\LugarTuristico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    /**
     * Guardar un nuevo comentario/reseña
     */
    public function store(Request $request, $lugarId)
    {
        // Validar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para dejar una reseña');
        }

        // Buscar el lugar
        $lugar = LugarTuristico::findOrFail($lugarId);

        // Validar los datos
        $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|min:10|max:1000',
        ], [
            'calificacion.required' => 'Debes seleccionar una calificación',
            'calificacion.min' => 'La calificación mínima es 1 estrella',
            'calificacion.max' => 'La calificación máxima es 5 estrellas',
            'comentario.required' => 'El comentario es obligatorio',
            'comentario.min' => 'El comentario debe tener al menos 10 caracteres',
            'comentario.max' => 'El comentario no puede superar los 1000 caracteres',
        ]);

        // Crear el comentario con estado 'aprobada'
        $nuevoComentario = Comentario::create([
            'user_id' => Auth::id(),
            'lugar_id' => $lugarId,
            'calificacion' => $request->calificacion,
            'comentario' => $request->comentario,
            'estado' => 'aprobada',
        ]);

        // Debug: verificar que se guardó correctamente
        \Log::info('Comentario creado:', [
            'id' => $nuevoComentario->id,
            'estado' => $nuevoComentario->estado,
            'lugar_id' => $nuevoComentario->lugar_id,
        ]);

        // Actualizar el promedio de calificación del lugar
        $lugar->actualizarPromedioCalificacion();

        return redirect()->back()->with('success', '¡Tu reseña ha sido publicada exitosamente!');
    }

    /**
     * Eliminar un comentario (solo el dueño o admin)
     */
    public function destroy($id)
    {
        $comentario = Comentario::findOrFail($id);

        // Verificar permisos: solo el autor o un admin pueden eliminar
        if (Auth::id() !== $comentario->user_id && !Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este comentario');
        }

        $lugarId = $comentario->lugar_id;
        $comentario->delete();

        // Actualizar promedio del lugar
        $lugar = LugarTuristico::find($lugarId);
        if ($lugar) {
            $lugar->actualizarPromedioCalificacion();
        }

        return redirect()->back()->with('success', 'Comentario eliminado correctamente');
    }

    /**
     * Moderar comentario (solo admin)
     */
    public function moderar(Request $request, $id)
    {
        // Verificar que sea admin
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'No tienes permiso para esta acción');
        }

        $comentario = Comentario::findOrFail($id);
        $accion = $request->input('accion'); // 'aprobar' o 'rechazar'

        if ($accion === 'aprobar') {
            $comentario->aprobar();
            $mensaje = 'Comentario aprobado correctamente';
        } elseif ($accion === 'rechazar') {
            $comentario->rechazar();
            $mensaje = 'Comentario rechazado correctamente';
        } else {
            return redirect()->back()->with('error', 'Acción no válida');
        }

        return redirect()->back()->with('success', $mensaje);
    }
}