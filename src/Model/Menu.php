<?php

namespace CloakWP\Eloquent\Model;

use CloakWP\Eloquent\Model\Menu\MenuLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use CloakWP\Eloquent\Traits\HasMeta;
use CloakWP\Eloquent\Model\Term\Relationships;
use CloakWP\Eloquent\Model\Term\Taxonomy;
use CloakWP\Eloquent\Model\Menu\MenuItem;

class Menu extends Model
{
  use HasMeta;

  protected $table = 'terms';
  protected $primaryKey = 'term_id';

  protected static function boot()
  {
    parent::boot();

    static::addGlobalScope('nav_menu', function (Builder $builder) {
      $builder->whereHas('termTaxonomy', function ($query) {
        $query->where('taxonomy', 'nav_menu');
      });
    });
  }

  public function menuItems()
  {
    return $this->hasManyThrough(
      MenuItem::class,
      Relationships::class,
      'term_taxonomy_id', // Foreign key on Term\Relationships table...
      'ID', // Foreign key on MenuItem table...
      'term_id', // Local key on Menu table...
      'object_id' // Local key on Term\Relationships table...
    )
      ->with('meta')
      ->orderBy('menu_order'); // Order by menu_order;
  }

  public function termTaxonomy()
  {
    return $this->hasOne(Taxonomy::class, 'term_id', 'term_id')->where('taxonomy', 'nav_menu');
  }

  public static function findBySlug($slug)
  {
    return static::where('slug', $slug)->first();
  }

  public function getStructuredMenu()
  {
    $menuItems = $this->menuItems()->get();
    $structuredMenuItems = $this->buildMenuTree($menuItems);

    return [
      'term_id' => $this->term_id,
      'name' => $this->name,
      'slug' => $this->slug,
      'term_group' => $this->term_group,
      'term_taxonomy_id' => $this->term_id,
      'taxonomy' => 'nav_menu',
      'count' => $this->termTaxonomy->count,
      'locations' => $this->getLocations(),
      'menu_items' => $structuredMenuItems
    ];
  }

  private function buildMenuTree($menuItems, $parentId = 0)
  {
    $branch = [];

    foreach ($menuItems as $menuItem) {
      $meta = $menuItem->formatted_meta;

      if ($meta['menu_item_parent'] == $parentId) {
        $children = $this->buildMenuTree($menuItems, $menuItem->ID);
        if ($children) {
          $meta['sub_menu_items'] = $children;
        } else {
          $meta['sub_menu_items'] = [];
        }
        $branch[] = $meta;
      }
    }

    usort($branch, function ($a, $b) {
      return $a['menu_order'] <=> $b['menu_order'];
    });

    return $branch;
  }

  public function getLocations()
  {
    $locations = MenuLocation::getMenuLocations();
    $menuLocations = [];

    foreach ($locations as $location => $menuId) {
      if ($menuId == $this->term_id) {
        $menuLocations[] = $location;
      }
    }

    return $menuLocations;
  }
}