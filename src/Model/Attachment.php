<?php

namespace CloakWP\Eloquent\Model;

class Attachment extends Post
{
  public function post()
  {
    return $this->belongsTo(Post::class, 'post_parent', 'ID');
  }
}
