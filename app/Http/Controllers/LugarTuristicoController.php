<?php

namespace App\Http\Controllers;

use App\Models\LugarTuristico;
use App\Models\Categoria;
use App\Models\Multimedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LugarTuristicoController extends Controller
{
    public function __construct()
    {
        // Solo los métodos de admin requieren autenticación
        $this->middleware(['auth', 'role:admin'])->except(['explorar', 'mostrar']);
    }

    // ==========================================
    // VISTA PÚBLICA - Explorar lugares (TODOS pueden acceder)
    // ==========================================
    public function explorar(Request $request)
    {
        $query = LugarTuristico::with('categoria', 'imagenes')
            ->where('visible', true); // Solo lugares visibles

        // Aplicar filtros
        if ($request->has('filtro')) {
            switch ($request->filtro) {
                case 'mejor-calificados':
                    $query->where('promedio_calificacion', '>=', 4) 
                          ->orderBy('promedio_calificacion', 'desc')
                          ->orderBy('id', 'desc');
                    break;
                
                case 'mas-economicos':
                    $query->where(function($q) {
                        $q->where('precio', '<=', 50000)
                          ->orWhereNull('precio')
                          ->orWhere('precio', 0);
                    })->orderByRaw('CASE WHEN precio IS NULL OR precio = 0 THEN 0 ELSE precio END ASC');
                    break;
                
                case 'mas-recientes':
                    $query->orderBy('created_at', 'desc');
                    break;
                
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $lugares = $query->get();
        $filtroActivo = $request->filtro ?? null;
        
        return view('lugares.explorar', compact('lugares', 'filtroActivo'));
    }

    // ==========================================
    //  VER DETALLE DE LUGAR PÚBLICO
    // ==========================================
    public function mostrar($id)
    {
        $lugare = LugarTuristico::with('categoria', 'imagenes', 'creador', 'comentarios.usuario')
        ->where('visible', true)
        ->findOrFail($id);
        
        return view('lugares.show', compact('lugare'));
    }

    // ==========================================
    //  VISTA ADMIN - Gestionar lugares
    // ==========================================
    public function index(Request $request)
    {
        $query = LugarTuristico::with('categoria', 'imagenes');

        // Aplicar filtros (admin ve todos, incluso no visibles)
        if ($request->has('filtro')) {
            switch ($request->filtro) {
                case 'mejor-calificados':
                    $query->where('promedio_calificacion', '>', 4)
                          ->orderBy('promedio_calificacion', 'desc')
                          ->orderBy('id', 'desc');
                    break;
                
                case 'mas-economicos':
                    $query->where(function($q) {
                        $q->where('precio', '<=', 50000)
                          ->orWhereNull('precio')
                          ->orWhere('precio', 0);
                    })->orderByRaw('CASE WHEN precio IS NULL OR precio = 0 THEN 0 ELSE precio END ASC');
                    break;
                
                case 'mas-recientes':
                    $query->orderBy('created_at', 'desc');
                    break;
                
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $lugares = $query->get();
        $filtroActivo = $request->filtro ?? null;
        
        return view('lugares.index', compact('lugares', 'filtroActivo'));
    }

    // ==========================================
    //  FORMULARIO DE CREACIÓN (Solo Admin)
    // ==========================================
    public function create()
    {
        $categorias = Categoria::all();
        return view('lugares.create', compact('categorias'));
    }

    // ==========================================
    // GUARDAR NUEVO LUGAR (Solo Admin)
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion' => 'nullable|string',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'horarios' => 'nullable|string',
            'precio' => 'nullable|numeric',
            'contacto' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'imagenes' => 'nullable|array|max:5',
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $lugar = LugarTuristico::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'direccion' => $request->direccion,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'horarios' => $request->horarios,
            'precio' => $request->precio,
            'contacto' => $request->contacto,
            'visible' => true,
            'promedio_calificacion' => 0,
            'categoria_id' => $request->categoria_id,
            'creador_id' => Auth::id(),
        ]);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $ruta = $imagen->store('lugares', 'public');
                Multimedia::create([
                    'tipo' => 'imagen',
                    'url' => $ruta,
                    'formato' => $imagen->getClientOriginalExtension(),
                    'tamano' => $imagen->getSize(),
                    'descripcion' => 'Imagen de ' . $lugar->nombre,
                    'lugar_id' => $lugar->id,
                ]);
            }
        }

        return redirect()->route('lugares.index')->with('success', 'Lugar turístico registrado correctamente.');
    }

    // ==========================================
    //  MOSTRAR DETALLES (Admin usa este)
    // ==========================================
    public function show(LugarTuristico $lugare)
    {
        $lugare->load('categoria', 'imagenes', 'creador', 'todasLasResenas.usuario');
        return view('lugares.show', compact('lugare'));
    }

    // ==========================================
    //  FORMULARIO DE EDICIÓN (Solo Admin)
    // ==========================================
    public function edit(LugarTuristico $lugare)
    {
        $categorias = Categoria::all();
        return view('lugares.edit', compact('lugare', 'categorias'));
    }

    // ==========================================
    // ACTUALIZAR LUGAR (Solo Admin)
    // ==========================================
    public function update(Request $request, LugarTuristico $lugare)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion' => 'nullable|string',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'horarios' => 'nullable|string',
            'precio' => 'nullable|numeric',
            'contacto' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $lugare->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'direccion' => $request->direccion,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'horarios' => $request->horarios,
            'precio' => $request->precio,
            'contacto' => $request->contacto,
            'categoria_id' => $request->categoria_id,
        ]);

        // ==========================================
        //  PROCESAR NUEVAS IMÁGENES (ESTO FALTABA)
        // ==========================================
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $ruta = $imagen->store('lugares', 'public');
                Multimedia::create([
                    'tipo' => 'imagen',
                    'url' => $ruta,
                    'formato' => $imagen->getClientOriginalExtension(),
                    'tamano' => $imagen->getSize(),
                    'descripcion' => 'Imagen de ' . $lugare->nombre,
                 'lugar_id' => $lugare->id,
                ]);
            }
        }

        return redirect()->route('lugares.index')->with('success', 'Lugar actualizado correctamente.');
    }

    // ==========================================
    //  ELIMINAR LUGAR (Solo Admin)
    // ==========================================
    public function destroy(LugarTuristico $lugare)
    {
        foreach ($lugare->imagenes as $img) {
            Storage::disk('public')->delete($img->url);
            $img->delete();
        }

        $lugare->delete();

        return redirect()->route('lugares.index')->with('success', 'Lugar eliminado correctamente.');
    }
}