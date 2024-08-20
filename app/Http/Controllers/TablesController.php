<?php

namespace Crater\Http\Controllers;

use Crater\Models\Table;
use Illuminate\Http\Request;

class TablesController extends Controller
{
    //
    public function index()
    {
        // Mostrar la lista de registros
        $tables = Table::all();

        return view('tables.index', compact('tables'));
    }

    public function create()
    {
        // Mostrar el formulario de creación
        return view('tables.create');
    }

    public function store(Request $request)
    {
        // Almacenar un nuevo registro
        Table::create($request->all());

        return redirect()->route('tables.index');
    }

    public function show($id)
    {
        // Mostrar un registro específico
        $table = Table::findOrFail($id);

        return view('tables.show', compact('table'));
    }
    //
}
