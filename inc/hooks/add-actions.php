<?php
#region Menu
function add_Main_Nav()
{
  register_nav_menu('menu', __('Menu'));
}
add_action('init', 'add_Main_Nav');
#endregion

#region CSS
function  theme_enqueue_styles()
{
  wp_enqueue_style('parent-style', get_template_directory_uri() .  '/style.css');
}
add_action('wp_enqueue_scripts',  'theme_enqueue_styles');

add_action('admin_enqueue_scripts', function () {
  wp_enqueue_style('galaxyy_admin', '/wp-content/themes/galaxyy/assets/css/admin.css');
});
#endregion