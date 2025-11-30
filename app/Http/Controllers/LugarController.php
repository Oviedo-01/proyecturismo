<?php

namespace App\Http\Controllers;

use App\Models\LugarTuristico;
use App\Models\Categoria;
use Illuminate\Http\Request;

class LugarController extends Controller
{
    /**
     * Mostrar listado público de lugares turísticos
     */
    public function index(Request $request)
    {
        $query = LugarTuristico::with(['categoria', 'imagenes'])
            ->where('visible', true);

        // Filtrar por categoría si se selecciona
        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria_id', $request->categoria);
        }

        // Filtrar por búsqueda de texto
        if ($request->has('buscar') && $request->buscar != '') {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%')
                  ->orWhere('direccion', 'like', '%' . $request->buscar . '%');
            });
        }

        /**
         *  Filtro rápido por menú desplegable
         */
        $filtroActivo = $request->get('filtro');

        switch ($filtroActivo) {

    case 'mejor-calificados':
        $query->orderBy('promedio_calificacion', 'desc');
        break;

    case 'mas-economicos':
        $query->where(function($q) {
            $q->where('precio', '<=', 50000)
              ->orWhereNull('precio')
              ->orWhere('precio', 0);
        })
        ->orderByRaw('CASE WHEN precio IS NULL OR precio = 0 THEN 0 ELSE precio END ASC');
        break;

    case 'mas-recientes':
        $query->orderBy('created_at', 'desc');
        break;

    default:
        if ($request->has('orden')) {
            switch ($request->orden) {
                case 'calificacion':
                    $query->orderBy('promedio_calificacion', 'desc');
                    break;
                case 'nombre':
                    $query->orderBy('nombre', 'asc');
                    break;
                case 'recientes':
                    $query->latest();
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }
}



        $lugares = $query->paginate(12);
        $categorias = Categoria::all();

        return view('lugares.index', compact('lugares', 'categorias', 'filtroActivo'));
    }

    /**
     * Mostrar detalles de un lugar específico
     */
    public function show($id)
    {
        $lugare = LugarTuristico::with([
            'categoria', 
            'imagenes', 
            'creador',
            'comentarios.usuario' 
        ])->findOrFail($id);
    
        return view('lugares.show', compact('lugare'));
    }
}
