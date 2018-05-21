<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conteudo extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'titulo', 'texto', 'data', 'image', 'link'
  ];
  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function comentarios()
  {
    return $this->hasMany('App\Comentario');
  }

  public function curtidas()
  {
    return $this->belongsToMany('App\User', 'curtidas', 'conteudo_id', 'user_id');
  }
}