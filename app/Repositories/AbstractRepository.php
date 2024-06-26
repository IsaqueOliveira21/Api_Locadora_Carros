<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
class AbstractRepository
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function selectAtributosRegistrosSelecionados($atributos)
    {
        $this->model = $this->model->with($atributos);
    }
    public function filtro($filtro)
    {
        $filtros = explode(';', $filtro);
        foreach($filtros as $key => $condicao) {
            $c = explode(':', $condicao);
            $this->model = $this->model->where($c[0], $c[1], $c[2]); // Aqui estamos utilizando o where na propria variavel pois acima ela ja foi instanciada no model
        }
    }
    public function selectAtributos($atributos)
    {
        $this->model = $this->model->selectRaw($atributos);
    }
    public function getResultado()
    {
        return $this->model->get();
    }
}
