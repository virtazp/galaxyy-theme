<?php
#region Affiche l'ordre des pages
add_action('admin_enqueue_scripts', function () {
  wp_enqueue_style('galaxyy_admin', '/wp-content/themes/galaxyy/assets/css/admin.css');
});

// Ajout d'une colonne pour l'ordre des pages
function add_order_column($columns)
{
  $columns['menu_order'] = 'Ordre';
  return $columns;
}
add_filter('manage_pages_columns', 'add_order_column');

// Affichage de l'ordre des pages dans la nouvelle colonne
function display_order_column($column, $post_id)
{
  if ('menu_order' === $column) {
    $post = get_post($post_id);
    echo $post->menu_order;
  }
}
add_action('manage_pages_custom_column', 'display_order_column', 10, 2);
#endregion