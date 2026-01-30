<?php
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
