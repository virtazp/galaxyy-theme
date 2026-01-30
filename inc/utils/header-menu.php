<?php
function wn_getMenu($nomdumenu)
{
    $array_menu = wp_get_nav_menu_items($nomdumenu);
    // var_dump($array_menu); 

    $menu = array();
    $submenu = array();
    foreach ($array_menu as $m) {
        // Si l'élément n'a pas de parent
        if (empty($m->menu_item_parent)) {
            $menu[$m->ID] = array();
            $menu[$m->ID]['ID']      =   $m->ID;
            $menu[$m->ID]['title']       =   $m->title;
            $menu[$m->ID]['url']         =   $m->url;
            $menu[$m->ID]['target']         =   $m->target;
            $menu[$m->ID]['attr_title']         =   $m->attr_title;
            $menu[$m->ID]['children']    =   array();
        } else {
            // S'il a un parent
            $submenu[$m->ID] = array();
            $submenu[$m->ID]['ID']       =   $m->ID;
            $submenu[$m->ID]['title']    =   $m->title;
            $submenu[$m->ID]['url']         =   $m->url;
            $submenu[$m->ID]['target']    =   $m->target;
            $menu[$m->menu_item_parent]['children'][$m->ID] = $submenu[$m->ID];
        }
    };

    // var_dump($menu); 
    return $menu;
}

function wn_formatMenu($menu)
{
    global $wp;

    $render = '';
    $_current_url = home_url($wp->request);
    $current_url = $_current_url . "/";

    foreach ($menu as $m) {

        $chevron = '';
        $childrenExist = '';
        $currentPage = '';
        $currentPage_2 = '';

        // S'il y a un sous menu à afficher
        if (!empty($m['children'])) {
            $chevron = '<span class="chevronj"><img src="' . render_media('header/chevron-open.svg') . '" alt="Dérouler le sous menu"></span>';
            $childrenExist = 'trueChildrenExist';
        }

        // Sur la page où l'on est dans le loop
        if ($current_url === $m['url']) {
            $currentPage = 'currentPage';
            $currentPage_2 = 'currentPage-2';
        }

        // S'il y a un sous menu à afficher
        if (!empty($m['children'])) {
            $render .= '<li class="' . $currentPage . ' itemMenu ' . $childrenExist . '" ><a href="' . $m['url'] . '" aria-current="page" class="aItemMenu" aria-label="' . $m['title'] . '">' . $m['title'] . ' ' . $chevron . ' ' . '</a>';

            // Sous-menu
            $render .= '<ul class="sousMenu">';
            foreach ($m['children'] as $sm) {
                if ($current_url === $sm['url']) {
                    $render .= '<li class="itemMenuSsMenu ' . $currentPage_2 . '"><a href="' . $sm['url'] . '" class="aItemMenuSsMenu" target="' . $sm['target'] . '" aria-label="' . $sm['title'] . '">' . $sm['title'] . '</a></li>';
                } else {
                    $render .= '<li class="itemMenuSsMenu"><a href="' . $sm['url'] . '" class="aItemMenuSsMenu" target="' . $sm['target'] . '" aria-label="' . $sm['title'] . '">' . $sm['title'] . '</a></li>';
                }
            }
            $render .= '</ul>';
        } else {
            $render .= '<li class="' . $currentPage . ' itemMenu"><a href="' . $m['url'] . '" aria-current="page" class="aItemMenu" aria-label="' . $m['title'] . '">' . $m['title'] . '</a>';
        }

        $render .= '</li>';
    }

    return $render;
}

function wn_formatMenuMobile($menu)
{
    // Récupérer l'URL du referer
    $current_url = wp_get_referer();

    // Vérifier si le referer est défini, sinon gérer l'absence
    if ($current_url) {
        $current_url = trailingslashit($current_url);
    } else {
        $current_url = ''; // Optionnel : Définit une valeur par défaut
    }

    $menuRetour = '<ul id="ul_menu_mobile" class="width-general">';

    foreach ($menu as $m) {

        // Normalise l'URL du menu
        $menu_url = trailingslashit($m['url']);

        // Comparer l'URL courante avec l'URL du menu
        $is_current_page = $current_url === $menu_url;

        if ($is_current_page) {
            if (!empty($m['children'])) {
                $menuRetour .= '<li class="li_menu_mobile">' .
                    '<a href="#" aria-current="page">' . $m['title'] . '</a>' .
                    '</li>';
            } else {
                $menuRetour .= '<li class="li_menu_mobile currentPage">' .
                    '<a href="' . $m['url'] . '" aria-current="page">' . $m['title'] . '</a>' .
                    '</li>';
            }

            if (!empty($m['children'])) {
                foreach ($m['children'] as $sm) {
                    $menuRetour .= '<li class="menu-mobile2"><a href="' . $sm['url'] . '">' . $sm['title'] . '</a></li>';
                }
            }
        } else {
            if (!empty($m['children'])) {
                $menuRetour .= '<li class="li_menu_mobile">' .
                    '<a href="#">' . $m['title'] . '</a>' .
                    '</li>';
            } else {
                $menuRetour .= '<li class="li_menu_mobile">' .
                    '<a href="' . $m['url'] . '">' . $m['title'] . '</a>' .
                    '</li>';
            }

            if (!empty($m['children'])) {
                foreach ($m['children'] as $sm) {
                    $menuRetour .= '<li class="menu-mobile2"><a href="' . $sm['url'] . '">' . $sm['title'] . '</a></li>';
                }
            }
        }
    }

    $menuRetour .= '</ul>';

    return $menuRetour;
}
