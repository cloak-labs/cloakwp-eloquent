<?php

namespace CloakWP\Eloquent\Model;

class Links extends \Illuminate\Database\Eloquent\Model
{
  protected $table = 'links';
  protected $primaryKey = 'link_id';
  public $timestamps = false;
}
