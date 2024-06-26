<?php

namespace CloakWP\Eloquent\Model\Post;

class Meta extends \Illuminate\Database\Eloquent\Model
{
  protected $table = 'postmeta';
  public $timestamps = false;
  protected $fillable = ['meta_key', 'meta_value'];
  protected $primaryKey = 'meta_id';


  public function post()
  {
    return $this->belongsTo(\CloakWP\Eloquent\Model\Post::class);
  }
}
