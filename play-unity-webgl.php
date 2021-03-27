<?php
/**
 * Plugin Name: Play Unity WebGL
 * Plugin URI: https://github.com/aallbrig/play-unity-webgl
 * Description: Upload your Unity WebGL builds and play through them on your wordpress site!
 * Version: v0.0.1
 * Author: Andrew Allbright
 * Author URI: https://www.andrewallbright.com
 */

include "admin/view.php";

function unity_webgl_games_admin_page_submit()
{
  // TODO: Handle admin page form submit
}

function unity_webgl_games_admin_page()
{
  $hookname = add_menu_page(
    'Unity WebGL Games',
    'Unity WebGL Games',
    'manage_options',
    'unity-webgl-games',
    'play_unity_webgl_page_html'
  );

  add_action('load-' . $hookname, 'unity_webgl_games_admin_page_submit');
}

function unity_webgl_games_setup_post_types()
{
  register_post_type('unity_webgl_game', [
    'labels' => [
      'name' => __('WebGL Games', 'textdomain'),
      'singular_name' => __('Game', 'textdomain')
    ],
    'public' => true,
    'has_archive' => true,
  ]);
}

function unity_webgl_games_activate()
{
  unity_webgl_games_setup_post_types();

  flush_rewrite_rules();
}

function unity_webgl_games_deactivate()
{
  unregister_post_type('game');

  flush_rewrite_rules();
}


function main()
{
  register_activation_hook(__FILE__, 'unity_webgl_games_activate');
  register_deactivation_hook(__FILE__, 'unity_webgl_games_deactivate');

  add_action('init', 'unity_webgl_games_setup_post_types');
  // Top-Level Menus (https://developer.wordpress.org/plugins/administration-menus/top-level-menus/)
  add_action('admin_menu', 'unity_webgl_games_admin_page');
}

main();