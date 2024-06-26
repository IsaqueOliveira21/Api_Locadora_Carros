<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);

        //$marcas = [];

        // --------- UTILIZANDO O REPOSITORY DE MARCA --------- //

        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosSelecionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosSelecionados('modelos');
        }
        if($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }
        if($request->has('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);
        }

        // ---------------------------------------------------- //
        /*
         // ------------ METODO DE BUSCAS QUE SE REPETE EM OUTROS CONTROLLERS (SUBSTITUIDO PELO REPOSITORY DESIGN PATTERN) ------------ //
        if($request->has('atributos_modelos')){
            $atributos_modelos = $request->atributos_modelos;
            $marcas = $this->marca->with('modelos:id,'.$atributos_modelos);
        } else {
            $marcas = $this->marca->with('modelos') ;
        }
        if($request->has('filtro')) {
            $filtros = explode(';', $request->filtro);
            foreach($filtros as $key => $condicao) {
                $c = explode(':', $condicao);
                $marcas = $marcas->where($c[0], $c[1], $c[2]); // Aqui estamos utilizando o where na propria variavel pois acima ela ja foi instanciada no model
            }
        }
        if($request->has('atributos')) {
            $atributos = $request->atributos;
            $marcas = $marcas->selectRaw($atributos)->get();
        } else {
            $marcas = $marcas->get();
        }
        */
        //$marcas = $this->marca->with('modelos')->get();
        //return $marcas;
        return response()->json($marcaRepository->getResultado(), 200);
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
        /* ESTA VALIDAÇÃO FOI DIRETAMENTE PARA O MODEL
        $regras = [
            'nome' => 'required|unique:marcas',
            'imagem' => 'required',
        ];
        $feedback = [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome da marca já existe'
        ];
        */

        $request->validate($this->marca->rules(), $this->marca->feedback());
        $imagem = $request->imagem;
        $imagem_urn = $imagem->store('imagens', 'public'); // sem o segundo parametro salva por padrao no diretorio local, se desejar salvar em um local especifico, utilize um segundo parametro
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
        ]);
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);
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
        // O METODO PUT E PATCH, QUANDO TRABALHADOS EM CONJUNTO COM O FORM DATA, NAO CONSEGUEM REALIZAR O REQUEST, ENTAO ALTERAMOS PARA POST E DEFINIMOS O METHOD
        $marca = $this->marca->find($id);
        if(is_null($marca)) {
            return response()->json(['erro' => 'Item não existe'], 404);
        }
        if($request->method() === 'PATCH') {
            $regrasDinamicas = [];
            foreach ($marca->rules() as $input => $regra) {
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas);
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }
        // Remove o arquivo antigo caso haja um novo no request
        if(!is_null($request->imagem)) {
            Storage::disk('public')->delete($marca->imagem);

        }
        $imagem = $request->imagem;
        $imagem_urn = $imagem->store('imagens', 'public');
        $marca->fill($request->all()); // utilizando este metodo para quando houver um PATCH, apenas sobrescrever oque houver alteração
        $marca->imagem = $imagem_urn;
        $marca->save();
        /*
        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
        ]);
        */
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
        Storage::disk('public')->delete($marca->imagem);
        $marca->delete();
        return $marca;
    }
}
