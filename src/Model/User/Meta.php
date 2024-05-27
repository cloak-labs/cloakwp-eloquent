<?php

namespace CloakWP\Eloquent\Model\User;

class Meta extends \Illuminate\Database\Eloquent\Model
{
  protected $table = 'usermeta';
  public $timestamps = false;
  protected $fillable = ['meta_key', 'meta_value'];
  protected $primaryKey = 'umeta_id';

  public function user()
  {
    return $this->belongsTo(\CloakWP\Eloquent\Model\User::class);
  }
}
