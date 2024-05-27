<?php

namespace CloakWP\Eloquent\Model\Term;

class Taxonomy extends \Illuminate\Database\Eloquent\Model
{
  protected $table = 'term_taxonomy';

  public function term()
  {
    return $this->belongsTo(\CloakWP\Eloquent\Model\Term::class, 'term_id', 'term_id');
  }
}
