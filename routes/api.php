<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;
use App\Conteudo;
use App\Comentario;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/cadastro', 'UserController@cadastro');

Route::post('/login', 'UserController@login');

Route::middleware('auth:api')->put('/atualizar', 'UserController@atualizar');

Route::middleware('auth:api')->post('/conteudo/cadastrar', 'ConteudoController@cadastrar');

Route::middleware('auth:api')->get('/conteudo/listar', 'ConteudoController@listar');

Route::middleware('auth:api')->put('/conteudo/curtir/{id}', 'ConteudoController@curtir');

Route::middleware('auth:api')->put('/conteudo/comentar/{id}', 'ConteudoController@comentar');

Route::post('/listarConteudo', 'UserController@listarConteudo');

Route::get('/testesAmigos', function ()
{
  $user = User::find(1);
  $user2 = User::find(2);
  //$user->amigos()->attach($user2->id);
  //$user->amigos()->detach($user2->id); remover todos
  $user->amigos()->toggle($user2->id);
  return $user->amigos;
});

Route::get('/testesCurtidas', function ()
{
  $user = User::find(1);
  $conteudo = Conteudo::find(1);
  $user->curtidas()->toggle($conteudo->id);
  return $conteudo->curtidas;
});


Route::get('/testesComentarios', function ()
{
  $user = User::find(1);
  $conteudo = Conteudo::find(1);
  $user->comentarios()->create([
    'conteudo_id' => $conteudo->id,
    'texto' => 'muito bom',
    'data' => date('Y-m-d')
  ]);
  return $conteudo->comentarios;
});
