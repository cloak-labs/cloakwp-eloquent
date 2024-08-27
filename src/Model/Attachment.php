<?php

namespace CloakWP\Eloquent\Model;

class Attachment extends Post
{
  public function post()
  {
    return $this->belongsTo(Post::class, 'post_parent', 'ID');
  }

  /**
   * Scope to filter attachments to images only (i.e. excludes videos, PDFs, etc.)
   */
  public function scopeImages($query)
  {
    return $query->where('post_type', 'attachment')
      ->where('post_mime_type', 'like', 'image/%');
  }

  /**
   * Scope to filter attachments by taxonomy term.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param array $termIdentifier an array of term IDs or slugs
   * @param string $taxonomy the taxonomy slug
   * @param string $identifierType ('id' or 'slug')
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeByTaxonomyTerm($query, $termIdentifiers, $taxonomy = 'category_media', $identifierType = 'id')
  {
    return $query->whereHas('terms.term', function ($q) use ($termIdentifiers, $taxonomy, $identifierType) {
      $q->where('taxonomy', $taxonomy);

      if ($identifierType === 'id') {
        $q->whereIn('term_id', (array) $termIdentifiers);
      } else {
        $q->whereIn('slug', (array) $termIdentifiers);
      }
    });
  }

  public function toArray()
  {
    $array = parent::toArray();
    return apply_filters('cloakwp/eloquent/attachments', $array);
  }
}
