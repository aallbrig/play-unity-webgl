<?php
/**
 * Plugin Name: Play Unity WebGL
 * Plugin URI: https://github.com/aallbrig/play-unity-webgl
 * Description: Upload your Unity WebGL builds and play through them on your wordpress site!
 * Version: v0.0.1
 * Author: Andrew Allbright
 * Author URI: https://www.andrewallbright.com
 */

const webgl_game_post_type = 'unity_webgl_game';

function unity_webgl_games_setup_post_types()
{
  register_post_type(webgl_game_post_type, [
    'labels' => [
      'name' => __('WebGL Games'),
      'singular_name' => __('Game'),
      'add_new' => __('New WebGL Game'),
      'add_new_item' => __('Add New WebGL Game'),
      'edit_item' => __('Edit WebGL Game'),
      'new_item' => __('New WebGL Game'),
      'view_item' => __('View WebGL Games'),
      'search_items' => __('Search WebGL Games'),
      'not_found' => __('No WebGL Games Found'),
      'not_found_in_trash' => __('No WebGL Games Found In Trash'),
    ],
    'public' => true,
    'has_archive' => true,
    'rewrite' => [
      'slug' => 'webglgames',
    ],
    'supports' => [
      'title',
      'comments',
      'thumbnail',
      'revisions',
      'page-attributes',
    ],
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

function webgl_game_zip_file_input_html($post)
{
  wp_nonce_field(plugin_basename(__FILE__), 'zip_file_input_nonce');

  ?>
    <label for="zip_file_input">WebGL Game Zip</label>
    <input type="file" id="zip_file_input" name="zip_file_input" value="" size="25"/>
  <?php
}

function webgl_game_zip_file_input_save($id)
{
  if (!wp_verify_nonce($_POST['zip_file_input_nonce'], plugin_basename(__FILE__))) {
    return $id;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $id;
  }

  if (webgl_game_post_type == $_POST['post_type']) {
    if (!current_user_can('edit_page', $id)) {
      return $id;
    }
  }

  if (!empty($_FILES['zip_file_input']['name'])) {
    $supported_types = ['application/zip'];
    $upload_file_type = wp_check_filetype(basename($_FILES['zip_file_input']['name']))['type'];

    if (in_array($upload_file_type, $supported_types)) {
      $upload = wp_upload_bits($_FILES['zip_file_input']['name'], null, file_get_contents($_FILES['zip_file_input']['tmp_name']));

      if (isset($upload['error']) && $upload['error'] != 0) {
        wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
      } else {
        add_post_meta($id, 'zip_file_input', $upload);
        update_post_meta($id, 'zip_file_input', $upload);
      }
    } else {
      wp_die('The file type you have uploaded is not supported');
    }
  }
}


function unity_webgl_games_webgl_input_meta_box()
{
  add_meta_box(
    'unity_webgl_game_game_zip_input_id',
    'Unity WebGL Build Zip',
    'webgl_game_zip_file_input_html',
    webgl_game_post_type
  );
}

function webgl_game_zip_file_update_edit_form()
{
   echo ' enctype="multipart/form-data"';
}

function main()
{
  register_activation_hook(__FILE__, 'unity_webgl_games_activate');
  register_deactivation_hook(__FILE__, 'unity_webgl_games_deactivate');

  add_action('init', 'unity_webgl_games_setup_post_types');

  // Custom input
  add_action('add_meta_boxes', 'unity_webgl_games_webgl_input_meta_box');
  add_action('save_post', 'webgl_game_zip_file_input_save');
  add_action('post_edit_form_tag', 'webgl_game_zip_file_update_edit_form');
}

main();