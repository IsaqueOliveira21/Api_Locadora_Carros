<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    private $marca;
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marcas = $this->marca->all();
        return $marcas;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $marca = $this->marca->create($request->all());
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $marca = $this->marca->find($id);
        if(is_null($marca)) {
            return response()->json(['erro' => 'Item não existe'], 404);
        }
        return $marca;
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // METODO PUT É UTILIZADO PARA ATUALIZAR TUDO
        // METODO PATCH É UTILIZADO PARA ATUALIZAÇÕES DE PARTES
        // TODOS OS DOIS FAZEM A MESMA COISA, A DIFEREÇA ENTRE NOMES É PARA IDENTIFICAÇÕES SEMANTICAS MELHOR
        $marca = $this->marca->find($id);
        if(is_null($marca)) {
            return response()->json(['erro' => 'Item não existe'], 404);
        }
        $marca->update($request->all());
        return $marca;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);
        if(is_null($marca)) {
            return response()->json(['erro' => 'Item não existe'], 404);
        }
        $marca->delete();
        return $marca;
    }
}
