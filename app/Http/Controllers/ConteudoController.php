<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Conteudo;
use App\User;
use Auth;

class ConteudoController extends Controller
{
  public function listar(Request $request)
  {
      //return Conteudo::with('user')->orderBy('data', 'desc')->get(); pega todos
      $conteudos = Conteudo::with('user')->orderBy('data', 'desc')->paginate(5);
      $user = $request->user();

      foreach ($conteudos as $key => $conteudo)
      {
          $conteudo->total_curtidas = $conteudo->curtidas()->count();
          $conteudo->total_comentarios = $conteudo->comentarios()->count();
          $conteudo->comentarios = $conteudo->comentarios()->with('user')->get();
          $conteudo->curtiu = ($user->curtidas()->find($conteudo->id)) ? true : false;
      }
      return [
        'status' => true,
        'conteudos' => $conteudos
      ];
  }

  public function cadastrar(Request $request)
  {

    $data = $request->all();
    $user = $request->user();
    $conteudo = new Conteudo;
    $conteudo->titulo = $data['titulo'];
    $conteudo->texto = $data['texto'];
    $conteudo->image = ($data['imagem']) ? $data['imagem'] : '#';
    $conteudo->link = ($data['link']) ? $data['link'] : '#';
    $conteudo->data = date('Y/m/d H:i:s');

    $valid = Validator::make($data,[
      'titulo' => 'required',
      'texto' => 'required'
    ]);

    if($valid->fails())
    {
      return [
              'status' => false,
              'validacao' => true,
              'erros' => $valid->errors()
            ];
    }

    $user->conteudos()->save($conteudo);
    $conteudos = Conteudo::with('user')->orderBy('data', 'desc')->paginate(5);
    return [
      'status' => true,
      'conteudos' => $conteudos
    ];
  }

  public function curtir($id, Request $request)
  {
    $conteudo = Conteudo::find($id);
    if($conteudo)
    {
      $user = $request->user();
      $user->curtidas()->toggle($conteudo->id);
      return [
        'status' => true,
        'curtidas' => $conteudo->curtidas()->count(),
        'lista' => $this->listar($request)
      ];
    }
    else
    {
      return [
        'status' => false,
        'erros' => 'Conteúdo não existe'
      ];
    }
  }
  public function comentar($id, Request $request)
  {
    $conteudo = Conteudo::find($id);
    if($conteudo)
    {
      $user = $request->user();
      $user->comentarios()->create([
        'conteudo_id' => $conteudo->id,
        'texto' => $request->texto,
        'data' => date('Y-m-d H:i:s')
      ]);
      return [
        'status' => true,
        'lista' => $this->listar($request)
      ];
    }
    else
    {
      return [
        'status' => false,
        'erros' => 'Conteúdo não existe'
      ];
    }
  }
}
