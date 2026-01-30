<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <?php
    get_template_part('inc/utils/analytics');
    ?>
    <title><?php bloginfo('name'); ?> &raquo; <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    wp_head();
    do_action('header_hook_file');
    ?>
</head>

<body <?php body_class(); ?>>
    <header id="main-header">
        <?php
        get_template_part('modules/header');
        ?>
    </header>