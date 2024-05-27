<?php

namespace CloakWP\Eloquent\Model;

use CloakWP\Eloquent\Traits\HasMeta;
use CloakWP\Eloquent\Traits\HasRoles;

class User extends \Illuminate\Database\Eloquent\Model
{

  use HasMeta, HasRoles;

  protected $table = 'users';
  protected $primaryKey = 'ID';
  public $timestamps = false;

  const CREATED_AT = 'user_registered';

  public function posts()
  {
    return $this->hasMany(\CloakWP\Eloquent\Model\Post::class, 'post_author')
      ->where('post_status', 'publish')
      ->where('post_type', 'post');
  }

  public function comments()
  {
    return $this->hasMany(\CloakWP\Eloquent\Model\Comment::class, 'user_id');
  }


  public function meta()
  {
    return $this->hasMany(\CloakWP\Eloquent\Model\User\Meta::class, 'user_id')
      ->select(['user_id', 'meta_key', 'meta_value']);
  }

}
