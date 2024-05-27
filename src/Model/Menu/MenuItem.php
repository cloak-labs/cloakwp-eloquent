<?php

namespace CloakWP\Eloquent\Model\Menu;

use CloakWP\Eloquent\Traits\HasMeta;
use Illuminate\Database\Eloquent\Model;
use CloakWP\Eloquent\Model\Term\Relationships;
use CloakWP\Eloquent\Model\Term\Taxonomy;
use CloakWP\Eloquent\Model\Menu;
use CloakWP\Eloquent\Model\Post\Meta;
use CloakWP\Eloquent\Model\Post;


class MenuItem extends Model
{
  use HasMeta;

  protected $table = 'posts';
  protected $primaryKey = 'ID';

  protected $appends = ['formatted_meta'];

  public function menu()
  {
    return $this->belongsToMany(
      Menu::class,
      Relationships::class,
      'object_id', // Foreign key on Term\Relationships table...
      'term_taxonomy_id', // Foreign key on Menu table...
      'ID', // Local key on MenuItem table...
      'term_id' // Local key on Term\Relationships table...
    )->whereHas('termTaxonomy', function ($query) {
      $query->where('taxonomy', 'nav_menu');
    });
  }

  public function termTaxonomy()
  {
    return $this->hasOne(Taxonomy::class, 'term_taxonomy_id', 'term_taxonomy_id');
  }

  public function meta()
  {
    return $this->hasMany(Meta::class, 'post_id', 'ID')->select(['post_id', 'meta_key', 'meta_value']);
  }

  public function getFormattedMetaAttribute()
  {
    $metaArray = [];
    foreach ($this->meta as $meta) {
      $key = str_replace('_menu_item_', '', $meta->meta_key);
      $metaArray[$key] = $meta->meta_value;
    }

    if (!empty($metaArray['object_id'])) {
      $post = Post::find($metaArray['object_id']);

      // Dynamically set the URL, title, and description if they are not set in meta
      if (empty($metaArray['url'])) {
        $metaArray['url'] = $post ? get_permalink($post->ID) : '#';
      }

      if (empty($metaArray['title'])) {
        $metaArray['title'] = !empty($this->post_title) ? $this->post_title : $post->post_title;
      }
    }

    if (empty($metaArray['description'])) {
      $metaArray['description'] = $this->post_content;
    }

    // Clean up the classes property
    if (!empty($metaArray['classes'])) {
      $metaArray['classes'] = $this->cleanUpClasses($metaArray['classes']);
    }

    // Add other necessary fields
    $metaArray['id'] = $this->ID;
    $metaArray['menu_order'] = $this->menu_order;
    $metaArray['link_type'] = $metaArray['type']; // rename `type` to `link_type` for clarity

    $propertiesToExclude = ['xfn', '_wp_old_date', 'type'];
    foreach ($propertiesToExclude as $prop)
      unset($metaArray[$prop]);

    $finalMeta = apply_filters('cloakwp/eloquent/model/menu_item/formatted_meta', $metaArray);

    return $finalMeta;
  }

  protected function cleanUpClasses($classes)
  {
    $classesArray = unserialize($classes);
    if (is_array($classesArray)) {
      return implode(' ', $classesArray);
    }
    return $classes;
  }
}