<?php
/**
 * Plugin Name: Play Unity WebGL
 * Plugin URI: https://github.com/aallbrig/play-unity-webgl
 * Description: Upload your Unity WebGL builds and play through them on your wordpress site!
 * Version: v0.0.1
 * Author: Andrew Allbright
 * Author URI: https://www.andrewallbright.com
 */

// TODO: Move this to different file
function play_unity_webgl_page_html()
{
  ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>
  <?php
}

// Top-Level Menus (https://developer.wordpress.org/plugins/administration-menus/top-level-menus/)
add_action('admin_menu', 'unity_webgl_games_page');

function unity_webgl_games_page()
{
  add_menu_page(
    'Unity WebGL Games',
    'Unity WebGL Games',
    'manage_options',
    'unity-webgl-games',
    'play_unity_webgl_page_html'
    );
}
