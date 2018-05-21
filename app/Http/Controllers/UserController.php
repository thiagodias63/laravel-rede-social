<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;
use Auth;

class UserController extends Controller
{
  public function login(Request $request)
  {
    $data = $request->all();
    $valid = Validator::make($data, [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string',
    ]);

    if($valid->fails())
    {
      return [
              'status' => false,
              'validacao' => true,
              'erros' => $valid->errors()
            ];
    }
    if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']]))
    {
      $user = auth()->user();
      //$user->image = asset($user->image);
      $user->token = $user->createToken($user->email)->accessToken;
      return [
              'status' => true,
              'usuario' => $user
            ];
    }
    else
    {
      return ['status' => false];
    }
  }
  public function cadastro(Request $request)
  {
    $data = $request->all();
    $valid = Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if($valid->fails())
    {
      return [
              'status' => false,
              'validacao' => true,
              'erros' => $valid->errors()
            ];
    }

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
        'image' => '/perfils/default_avatar.png'
    ]);

    $user->token = $user->createToken($user->email)->accessToken;
    //$user->image = asset($user->image);
    return [
            'status' => true,
            'usuario' => $user
          ];
  }
  public function atualizar(Request $request)
  {
    $user = $request->user();
    $data = $request->all();
    if(isset($data['password']))
    {
      $valid = Validator::make($data, [
          'name' => 'required|string|max:255',
          'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id)],
          'password' => 'required|string|min:6|confirmed',
      ]);
      if($valid->fails())
      {
        return [
                'status' => false,
                'validacao' => true,
                'erros' => $valid->errors()
              ];
      }
      $user->password = bcrypt($data['password']);
    }
    else
    {
      $valid = Validator::make($data, [
          'name' => 'required|string|max:255',
          'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id)]
      ]);
      if($valid->fails())
      {
        return [
                'status' => false,
                'validacao' => true,
                'erros' => $valid->errors()
              ];
      }
    }
    if(isset($data['image']))
    {
      Validator::extend('base64image', function($attribute, $value, $parameters, $validator) {
        $explode = explode(',', $value);
        $allow = ['png', 'jpg', 'svg','jpeg'];
        $format = str_replace(
          [
              'data:image/',
              ';',
              'base64'
          ],
          [
            '','','',
          ],
          $explode[0]
        );
        //check file
        if (!in_array($format, $allow))
        {
          return false;
        }
        if(!preg_match('%^[a-zA-z0-9/+]*={0,2}$%', $explode[1]))
        {
          return false;
        }
        return true;
      });

      $valid = Validator::make($data,
        ['image' => 'base64image'],
        ['base64image' => 'Imagem enviada não é válida']
      );

      if($valid->fails())
      {
        return [
                'status' => false,
                'validacao' => true,
                'erros' => $valid->errors()
              ];
        }

      $time = time();
      $diretorioPai = 'perfils';
      $diretorioImagem = $diretorioPai.DIRECTORY_SEPARATOR.'perfil_id_'.$user->id;
      $ext = substr($data['image'], 11, strpos($data['image'], ';') -  11);
      $urlImagem = $diretorioImagem.DIRECTORY_SEPARATOR.$time.'.'.$ext;

      $file = str_replace('data:image/'.$ext.';base64,','',$data['image']);
      $file = base64_decode($file);

      if(!file_exists($diretorioPai))
      {
        mkdir($diretorioPai,0700);
      }
      $imgUser = str_replace(asset('/'),'',$user->image)
      if($imgUser)
      {
        if(file_exists($imgUser))
        {
          unlink($imgUser);
        }
      }
      if(!file_exists($diretorioImagem))
      {
        mkdir($diretorioImagem,0700);
      }
      file_put_contents($urlImagem,$file);
      $user->image = $urlImagem;
    }

    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->save();
    //$user->image = asset($user->image);
    $user->token = $user->createToken($user->email)->accessToken;
    return [
            'status' => true,
            'usuario' => $user
          ];
  }

  /* Conteudos
  public function cadastrarConteudo(Request $request)
  {
    $data = $request->all();
    $user = User::find($data['user']);
    $user->conteudos()->create([
      'titulo' => $data['titulo'],
      'texto' => $data['texto'],
      'image' => 'https://complex-res.cloudinary.com/images/c_limit,f_auto,fl_lossy,q_auto,w_1030/fuq3xctzwvnhpxegpolx/shiloh-dynasty',
      'link' => $data['link'],
      'data' => date('Y/m/d h:i'),
    ]);
    return $user->conteudos;
  }
  */
  public function listarConteudo(Request $request)
  {
    $data = $request->all();
    $user = User::find($data['user']);
    return $user->conteudos;
  }
}
