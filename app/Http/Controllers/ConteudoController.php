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
      return [
        'status' => true,
        'conteudos' => $conteudos
      ];
  }

  public function cadastrar(Request $request)
  {

    $data = $request->all();
    $user = $request->user();
    //return $data;
    //$user = User::find($data['user']['id']);
    $conteudo = new Conteudo;
    $conteudo->titulo = $data['titulo'];
    $conteudo->texto = $data['texto'];
    $conteudo->image = ($data['imagem']) ? $data['imagem'] : '#';
    $conteudo->link = ($data['link']) ? $data['link'] : '#';
    $conteudo->data = date('Y/m/d H:i:s');

    $user->conteudos()->save($conteudo);
    //return $conteudo;
    // return [
    //     'status' => true,
    //     'conteudos' => $user->conteudos
    //   ];
    $conteudos = Conteudo::with('user')->orderBy('data', 'desc')->paginate(5);
    return [
      'status' => true,
      'conteudos' => $conteudos
    ];
  }

}
