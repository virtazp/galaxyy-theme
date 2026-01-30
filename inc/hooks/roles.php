<?php
function add_custom_role()
{
    if (!get_option('professionnel_roles_version')) {
        $capabilities = array(
            'edit_posts' => true,
            'upload_files' => true,
            'manage_options' => true
        );
        add_role('professionnel', 'Professionnel', $capabilities);
        update_option('professionnel_roles_version', 1);
    }
}
// add_action('init', 'add_custom_role');
