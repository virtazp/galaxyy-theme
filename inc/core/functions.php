<?php
#region Fonctions générales
function safe_print($content, $key, $class, $tag = 'p', $index_cta = 0, $attributes = [])
{
  // Vérification de l'existence et du contenu
  if (!isset($content[$key]) || empty($content[$key])) {
    return;
  }

  // Construction de la classe CSS
  $class_attr = '';
  if (!empty($class)) {
    $class_attr = 'class="' . esc_attr($class) . '"';
  }

  // Gestion du tag lien
  if ($tag === 'a') {
    $href = ($index_cta != 0) ? $content['cta_' . $index_cta] : $content['cta'];
    $tag = 'a href="' . esc_url($href) . '"';

    if (isset($content['target_blank']) && $content['target_blank'] == 1) {
      $tag .= ' target="_blank"';
    }

    if (isset($content['target_blank' . $index_cta]) && $content['target_blank' . $index_cta] == 1) {
      $tag .= ' target="_blank"';
    }
  }

  // Ajout d'attributs supplémentaires
  $extra_attrs = build_extra_attributes($attributes);

  // Génération du tag standard
  echo '<' . $tag . ' ' . $class_attr . $extra_attrs . '>' . wp_kses_post($content[$key]) . '</' . explode(' ', $tag)[0] . '>';
}

/**
 * Construit une chaîne d'attributs supplémentaires
 */
function build_extra_attributes($attributes)
{
  if (empty($attributes)) {
    return '';
  }

  $attrs = [];
  foreach ($attributes as $attr => $value) {
    if (!empty($value)) {
      $attrs[] = esc_attr($attr) . '="' . esc_attr($value) . '"';
    }
  }

  return !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
}

/**
 * Affiche les boutons ACF depuis un flexible_content
 * dans le cas où on laisse la possibilité de mettre plusieurs boutons
 * dans un module de manière flexible
 *
 * @param array $buttons  Le tableau de boutons ACF (flexible_content)
 * @param string $layout  Le nom du layout à filtrer (par défaut 'bouton')
 * @return void
 */
function render_acf_buttons($buttons, $layout = 'bouton')
{
  if (empty($buttons) || !is_array($buttons)) {
    return;
  }

  foreach ($buttons as $row) {
    // Si le layout est un tableau, on parcourt le tableau
    // Et qu'il est issu d'un flexible_content
    if (isset($row['acf_fc_layout']) && $row['acf_fc_layout'] == $layout) {
      $btn = $row[$layout] ?? null;
      if ($btn) {
        get_template_part('parts/btn', null, array('btn' => $btn));
      }
    } else {
    }
  }
}


/**
 * Affiche une image ou un SVG en fonction de son type
 * Peut aussi prendre une URL d'image directement (dans ce cas, $attachment_id est l'URL)
 *
 * @param int|string $attachment_id L'ID de l'attachement OU une URL de l'image
 * @param string $size La taille de l'image (ignorée si URL)
 * @return void
 */
function render_media($attachment_id, $size = 'full')
{
  // Si $attachment_id est une URL directe, on affiche l'image avec une balise <img>
  if (is_string($attachment_id) && (
    filter_var($attachment_id, FILTER_VALIDATE_URL) ||
    preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $attachment_id)
  )) {
    // On tente de deviner si c'est un SVG pour éventuellement l'inliner
    if (preg_match('/\.svg$/i', $attachment_id)) {
      // Essayer d'inliner le SVG si accessible localement
      $path = ABSPATH . str_replace(home_url('/'), '', $attachment_id);
      if (file_exists($path)) {
        echo file_get_contents($path);
      } else {
        // Sinon, fallback : balise img
        echo '<img src="' . esc_url($attachment_id) . '" alt="">';
      }
    } else {
      echo '<img src="' . esc_url($attachment_id) . '" alt="">';
    }
    return;
  }

  // Sinon, workflow par ID d'attachement classique
  $mime = get_post_mime_type($attachment_id);

  // SVG → inline
  if ($mime === 'image/svg+xml') {
    $path = get_attached_file($attachment_id);
    if (file_exists($path)) {
      echo file_get_contents($path); // inline SVG
    }
  }
  // Autres images → normal
  else {
    echo wp_get_attachment_image($attachment_id, $size);
  }
}

#endregion

#region Enqueue js et css

/**
 * Enqueue un script module. Ne charge pas le script plusieurs fois fois en cas de double appel.
 * @param string $handle Le handle du script
 * @param string $path Le chemin du script
 * @param boolean $localise Indique si le script doit être localisé
 * @param string $object_name Le nom de l'objet localisé
 * @param array $localise_data Les données localisées
 */
function galaxyy_enqueue_module_script($handle, $path, $localise = false, $object_name = '', $localise_data = [])
{
  wp_enqueue_script(
    $handle,
    get_template_directory_uri() . $path,
    array(),
    filemtime(get_template_directory() . $path),
    true
  );

  if ($localise && !empty($object_name) && !empty($localise_data)) {
    wp_localize_script($handle, $object_name, $localise_data);
  }
}



/**
 * Enqueue une feuille de style CSS. Ne charge pas la feuille plusieurs fois en cas de double appel.
 * @param string $handle Le handle du style
 * @param string $path Le chemin du fichier CSS (relatif au thème)
 */
function galaxyy_enqueue_module_style($handle, $path)
{
  wp_enqueue_style(
    $handle,
    get_template_directory_uri() . $path,
    array(),
    filemtime(get_template_directory() . $path)
  );
}

#endregion

#region Fil d'Ariane

// Fonction pour générer un lien pour chaque segment du fil d'Ariane
function generate_breadcrumb_link($url, $name)
{
  return '<a href="' . esc_url($url) . '">' . esc_html($name) . '</a><span class="chevron"> <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
  <path opacity="0.2" d="M8.08398 6L4.61719 9.4668V2.5332L8.08398 6Z" fill="#006178" stroke="#006178" stroke-width="0.234375"/>
  <path d="M8.51531 5.73468L4.76531 1.98468C4.71287 1.93217 4.64602 1.89641 4.57324 1.88192C4.50046 1.86742 4.42501 1.87485 4.35645 1.90326C4.28789 1.93166 4.22931 1.97977 4.1881 2.0415C4.1469 2.10322 4.12494 2.17578 4.125 2.24999V9.74999C4.12494 9.8242 4.1469 9.89676 4.1881 9.95848C4.22931 10.0202 4.28789 10.0683 4.35645 10.0967C4.42501 10.1251 4.50046 10.1326 4.57324 10.1181C4.64602 10.1036 4.71287 10.0678 4.76531 10.0153L8.51531 6.2653C8.55018 6.23047 8.57784 6.18912 8.59671 6.14359C8.61558 6.09807 8.62529 6.04927 8.62529 5.99999C8.62529 5.95071 8.61558 5.90191 8.59671 5.85639C8.57784 5.81086 8.55018 5.7695 8.51531 5.73468ZM4.875 8.84483V3.15515L7.71984 5.99999L4.875 8.84483Z" fill="#006178"/>
  </svg></span>';
}

// Fonction pour créer le fil d'Ariane
function create_breadcrumb()
{
  global $wp;
  $home_url = get_site_url();
  $current_url = home_url($wp->request) . "/";
  $title = wp_title('', false);

  // Récupérer et segmenter l'URL actuelle
  $relative_url = str_replace($home_url, '', $current_url);
  $segments = array_values(array_filter(explode('/', $relative_url)));

  // Ne pas afficher le fil d'Ariane pour les pages de niveau 1 (segments vides ou un seul segment)
  if (count($segments) < 2) {
    return;
  }

  // Commencer la génération du fil d'Ariane
  $breadcrumb = '<nav id="ariane" aria-label="Breadcrumb"><ul id="firstContainerAriane" class="width-general">';

  // Ajouter le lien vers la page d'accueil
  $breadcrumb .= '<li id="firstAriane">' . generate_breadcrumb_link($home_url, 'Accueil') . '</li>';

  // Ajouter les segments de l'URL (jusqu'à 3 niveaux de profondeur)
  $url_accumulator = $home_url;
  $max_depth = min(3, count($segments)); // Limiter à 3 niveaux

  for ($i = 0; $i < $max_depth; $i++) {
    $segment = $segments[$i];
    $url_accumulator .= '/' . $segment;

    // Récupérer le titre de la page correspondante
    $page_title = get_page_title_by_url($url_accumulator);

    // Si pas de titre trouvé, utiliser le segment transformé comme fallback
    if (empty($page_title)) {
      $page_title = str_replace('-', ' ', $segment);
    }

    if ($i < $max_depth - 1) {
      // Ajouter le segment comme lien cliquable
      $breadcrumb .= '<li>' . generate_breadcrumb_link($url_accumulator, $page_title) . '</li>';
    } else {
      // Ajouter le dernier segment comme élément non cliquable
      $breadcrumb .= '<li><span aria-current="page"><b>' . esc_html($page_title) . '</b></span></li>';
    }
  }

  // Fermer les balises de la liste et de la navigation
  $breadcrumb .= '</ul></nav>';

  // Afficher le fil d'Ariane
  echo $breadcrumb;
}

/**
 * Récupère le titre d'une page à partir de son URL
 * @param string $url L'URL de la page
 * @return string Le titre de la page ou une chaîne vide si non trouvée
 */
function get_page_title_by_url($url)
{
  // Nettoyer l'URL
  $url = rtrim($url, '/');

  // Essayer de récupérer la page par son URL
  $page_id = url_to_postid($url);

  if ($page_id) {
    return get_the_title($page_id);
  }

  // Si pas trouvé avec url_to_postid, essayer avec get_page_by_path
  $path = str_replace(home_url(), '', $url);
  $path = ltrim($path, '/');

  if (!empty($path)) {
    $page = get_page_by_path($path);
    if ($page) {
      return $page->post_title;
    }
  }

  // Essayer avec une requête WP_Query pour les pages
  $query = new WP_Query(array(
    'post_type' => 'page',
    'post_status' => 'publish',
    'meta_query' => array(
      array(
        'key' => '_wp_page_template',
        'value' => '',
        'compare' => 'EXISTS'
      )
    ),
    'posts_per_page' => 1,
    'fields' => 'ids'
  ));

  // Essayer de matcher avec le slug
  $slug = basename($path);
  if (!empty($slug)) {
    $page = get_page_by_path($slug);
    if ($page) {
      return $page->post_title;
    }
  }

  return '';
}

#endregion