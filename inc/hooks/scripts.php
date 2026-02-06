<?php
#region Scripts général
add_action('wp_enqueue_scripts', 'add_scripts');
function add_scripts()
{
  wp_enqueue_script(
    'script',
    get_template_directory_uri() . '/assets/js/script.js',
    array(),
    '1.0.0',
    true // Le paramètre true charge déjà le script dans le footer
  );

  wp_localize_script('script', 'ajax_object', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'pathSite' => get_site_url(),
  ]);
}
#endregion

#region Scripts Actualités
// add_action('wp_enqueue_scripts', 'galaxyy_conditional_scripts');
function galaxyy_conditional_scripts()
{
  // Archive actualités
  if (is_post_type_archive('example_post_type')) {

    wp_enqueue_script(
      'galaxyy-example_post_type',
      get_template_directory_uri() . '/assets/js/example_post_type-archive.js',
      [],
      '1.0.0',
      true
    );

    wp_localize_script('galaxyy-example_post_type', 'GALAXYY_EXAMPLE_POST_TYPE', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'site_url' => get_site_url(),
    ]);
  }
}
#endregion