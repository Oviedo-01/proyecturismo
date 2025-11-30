<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // 🟢 Mostrar lista de categorías
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('categorias.index', compact('categorias'));
    }

    // 🟢 Formulario de creación
    public function create()
    {
        return view('categorias.create');
    }

    // 🟢 Guardar nueva categoría
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:500',
        ]);

        Categoria::create($request->only('nombre', 'descripcion'));

        return redirect()->route('categorias.index')->with('success', 'Categoría agregada correctamente.');
    }

    // 🟢 Eliminar categoría
    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }
}



