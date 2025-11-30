<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Categoria;
use App\Models\LugarTuristico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventoController extends Controller
{
    /**
     * Constructor: solo admin puede crear/editar/eliminar
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except(['index', 'show']);
    }

    /**
     * Mostrar todos los eventos (público)
     */
    public function index()
    {
        $eventos = Evento::with(['categoria', 'lugar', 'creador'])
            ->activos()
            ->proximos()
            ->orderBy('fecha_inicio', 'asc')
            ->paginate(12);

        return view('eventos.index', compact('eventos'));
    }

    /**
     * Formulario para crear evento (admin)
     */
    public function create()
    {
        $categorias = Categoria::all();
        $lugares = LugarTuristico::where('visible', true)->get();
        
        return view('eventos.create', compact('categorias', 'lugares'));
    }

    /**
     * Guardar nuevo evento
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'nullable|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'lugar_id' => 'nullable|exists:lugar_turisticos,id',
        ], [
            'nombre.required' => 'El nombre del evento es obligatorio',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'capacidad.required' => 'La capacidad es obligatoria',
            'capacidad.min' => 'La capacidad mínima es 1 persona',
            'precio.required' => 'El precio es obligatorio (usa 0 para eventos gratuitos)',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior a la fecha de inicio',
        ]);

        Evento::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'ubicacion' => $request->ubicacion,
            'capacidad' => $request->capacidad,
            'precio' => $request->precio,
            'estado' => 'activo',
            'categoria_id' => $request->categoria_id,
            'lugar_id' => $request->lugar_id,
            'creador_id' => Auth::id(),
        ]);

        return redirect()->route('eventos.index')->with('success', 'Evento creado exitosamente');
    }

    /**
     * Mostrar detalles de un evento (público)
     */
    public function show($id)
    {
        $evento = Evento::with(['categoria', 'lugar', 'creador', 'reservasConfirmadas.usuario'])
            ->findOrFail($id);

        return view('eventos.show', compact('evento'));
    }

    /**
     * Formulario de edición (admin)
     */
    public function edit($id)
    {
        $evento = Evento::findOrFail($id);
        $categorias = Categoria::all();
        $lugares = LugarTuristico::where('visible', true)->get();

        return view('eventos.edit', compact('evento', 'categorias', 'lugares'));
    }

    /**
     * Actualizar evento
     */
    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'nullable|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:activo,cancelado,finalizado',
            'categoria_id' => 'required|exists:categorias,id',
            'lugar_id' => 'nullable|exists:lugar_turisticos,id',
        ]);

        $evento->update($request->all());

        return redirect()->route('eventos.index')->with('success', 'Evento actualizado correctamente');
    }

    /**
     * Eliminar evento
     */
    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        
        // Verificar si hay reservas
        if ($evento->reservasConfirmadas()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar un evento con reservas confirmadas. Cancela el evento en su lugar.');
        }

        $evento->delete();

        return redirect()->route('eventos.index')->with('success', 'Evento eliminado correctamente');
    }
}