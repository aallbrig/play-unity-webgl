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
      'slug' => 'webgl-games',
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

  $value = get_post_meta($post->ID, 'zip_file_input', true);
  $html = '';

  if (isset($value)) {
    $html .= '<h4>Upload: ' . basename($value['file']) . '</h4>';
  }

  $html .= '<label for="zip_file_input">Upload new Unity WebGL Build Zip</label><br/>';
  $html .= '<input type="file" id="zip_file_input" name="zip_file_input" value="" size="25"/>';

  echo $html;
}

function validate_unity_webgl_file($file)
{
  $supported_file_types = [
    ['ext' => 'js', 'type' => 'application/javascript'],
    ['ext' => 'unityweb', 'type' => 'application/vnd.unity'],
    ['ext' => 'json', 'type' => 'application/json'],
  ];

  $file_type = wp_check_filetype($file);

  $matched_supported_file_type = array_filter($supported_file_types, function ($var) use ($file_type) {
    return $file_type['ext'] == $var['ext'] && $file_type['type'] == $var['type'];
  });

  if (count($matched_supported_file_type) == 0) {
    return false;
  }

  // Only UnityLoader.js is the ONLY supported JS file
  if ($file_type['ext'] == 'js' && basename($file) != 'UnityLoader.js') {
    return false;
  }

  return true;
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
      // Replace with unzip & verify a unity webGL build

      $tmp_filepath = $_FILES['zip_file_input']['tmp_name'];
      WP_Filesystem();
      $tmp_dir = path_join(get_temp_dir(), uniqid());

      $create = wp_mkdir_p($tmp_dir);
      if (!$create) {
        wp_die('Zip verification unsuccessful. Cannot create new temp directory.');
      }

      $files = list_files($tmp_dir);
      if (count($files) != 0) {
        wp_die('Zip verification unsuccessful. Newly created temp dir is mysteriously not empty.');
      }

      $unzipSuccess = unzip_file($tmp_filepath, $tmp_dir);
      if (is_wp_error($unzipSuccess)) {
        wp_die('Zip verification unsuccessful. Cannot unzip uploaded file to temp directory.');
      }

      $buildFiles = list_files(path_join($tmp_dir, 'Build'));
      if (count($buildFiles) == 0) {
        wp_die('Zip verification unsuccessful. Cannot find Build directory in uploaded ZIP.');
      }

      foreach ($buildFiles as $buildFile) {
        if (!validate_unity_webgl_file($buildFile)) {
          wp_die('Detected invalid webGL build file in build directory.');
        }
      }

      foreach ($buildFiles as $buildFile) {
        $upload = wp_upload_bits(basename($buildFile), null, file_get_contents($buildFile));

        if (isset($upload['error']) && $upload['error'] != 0) {
          wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
        } else {
          add_post_meta($id, 'zip_file_input', $upload);
          update_post_meta($id, 'zip_file_input', $upload);
        }
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

function unity_webgl_games_custom_upload_mimes($existing_mimes)
{
  $existing_mimes['unityweb'] = 'application/vnd.unity';
  $existing_mimes['json'] = 'application/json';

  return $existing_mimes;
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

  add_filter('mime_types', 'unity_webgl_games_custom_upload_mimes');
}

main();