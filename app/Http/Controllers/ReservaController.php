<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    /**
     * Constructor: requiere autenticación
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Mostrar las reservas del usuario autenticado
     */
    public function index()
    {
        $reservas = Reserva::with(['evento.categoria', 'evento.lugar'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reservas.index', compact('reservas'));
    }

    /**
     * Inscribirse a un evento (crear reserva)
     */
    public function store(Request $request)
    {
        $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'notas' => 'nullable|string|max:500',
        ]);

        $evento = Evento::findOrFail($request->evento_id);

        // Validaciones
        if ($evento->estado !== 'activo') {
            return redirect()->back()->with('error', 'Este evento no está disponible para inscripciones');
        }

        if (!$evento->tieneCupos()) {
            return redirect()->back()->with('error', 'Lo sentimos, este evento ya no tiene cupos disponibles');
        }

        if ($evento->usuarioEstaInscrito(Auth::id())) {
            return redirect()->back()->with('error', 'Ya estás inscrito en este evento');
        }

        if ($evento->haTerminado()) {
            return redirect()->back()->with('error', 'Este evento ya finalizó');
        }

        // Crear la reserva
        Reserva::create([
            'user_id' => Auth::id(),
            'evento_id' => $request->evento_id,
            'estado' => 'confirmada',
            'notas' => $request->notas,
        ]);

        return redirect()->route('reservas.index')->with('success', '¡Te has inscrito exitosamente al evento!');
    }

    /**
     * Cancelar una reserva
     */
    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);

        // Verificar que sea el dueño de la reserva
        if ($reserva->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para cancelar esta reserva');
        }

        // Verificar que el evento no haya empezado
        if ($reserva->evento->estaEnCurso() || $reserva->evento->haTerminado()) {
            return redirect()->back()->with('error', 'No puedes cancelar una reserva de un evento que ya comenzó o finalizó');
        }

        $reserva->cancelar();

        return redirect()->back()->with('success', 'Reserva cancelada correctamente');
    }
}