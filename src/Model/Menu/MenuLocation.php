<?php

namespace CloakWP\Eloquent\Model\Menu;

use Illuminate\Database\Eloquent\Model;
use CloakWP\Eloquent\Model\Menu;

class MenuLocation extends Model
{
  protected $table = 'options';
  protected $primaryKey = 'option_id';
  public $timestamps = false;

  protected $fillable = ['option_name', 'option_value'];

  public static function getMenuLocations()
  {
    // Get the current theme folder name
    $theme = wp_get_theme();
    $themeSlug = $theme->get_stylesheet();
    $optionName = 'theme_mods_' . $themeSlug;
    $option = static::where('option_name', $optionName)->first();

    if ($option) {
      $data = unserialize($option->option_value);
      return isset($data['nav_menu_locations']) ? $data['nav_menu_locations'] : [];
    }

    return [];
  }

  public static function getMenuByLocation($location)
  {
    $locations = static::getMenuLocations();

    if (isset($locations[$location])) {
      $menuId = $locations[$location];
      return Menu::find($menuId);
    }

    return null;
  }
}
