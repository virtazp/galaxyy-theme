<?php
#region Général
include get_template_directory() . '/inc/hooks/add-actions.php';
include get_template_directory() . '/inc/hooks/scripts.php';
include get_template_directory() . '/inc/hooks/remove-actions.php';
include get_template_directory() . '/inc/utils/header-menu.php';
#endregion

#region class
#endregion

#region Fonctions supplémentaires
require_once('inc/admin/add-columns-admin.php');
require_once('inc/core/functions.php');
#endregion

#region Ajax
include get_template_directory() . '/inc/ajax/menu.php';
#endregion
