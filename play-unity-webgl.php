<?php
/**
 * Plugin Name: Play Unity WebGL
 * Plugin URI: https://github.com/aallbrig/play-unity-webgl
 * Description: Upload your Unity WebGL builds and play through them on your wordpress site!
 * Version: v0.0.1
 * Author: Andrew Allbright
 * Author URI: https://www.andrewallbright.com
 */

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
  unregister_post_type('unity_webgl_game');

  flush_rewrite_rules();
}


function main()
{
  register_activation_hook(__FILE__, 'unity_webgl_games_activate');
  register_deactivation_hook(__FILE__, 'unity_webgl_games_deactivate');

  add_action('init', 'unity_webgl_games_setup_post_types');
}

main();