<?php

// Fonctionnalités principales :

// Analyse d'URL : Découpe l'URL en segments et teste les parents (ex: /formation/etude-de-cas → /formation/)
// Suggestions automatiques : Propose les sections parentes qui existent réellement
// Alternatives populaires : Affiche les pages principales et articles récents
// Recherche intégrée : Formulaire de recherche WordPress
// Design responsive : S'adapte aux mobiles

// Avantages :

// Fonctionne avec tous les CPT futurs sans configuration
// Vérifie l'existence réelle des URLs avant de les proposer
// Interface utilisateur claire et engageante
// Performance optimisée avec des requêtes limitées

// TODO :

// Personnaliser les messages selon le type de contenu (CPT spécifiques)

function css_header()
{
  add_css_file("404.css");
}
add_action('header_hook_file', 'css_header', 10, 0);

get_header();


/**
 * Analyse l'URL 404 et génère des suggestions de redirection
 */
function get_smart_404_suggestions()
{
  $current_url = $_SERVER['REQUEST_URI'];
  $suggestions = [];

  // Nettoyer l'URL (enlever les slashes de début/fin)
  $path = trim(parse_url($current_url, PHP_URL_PATH), '/');

  if (empty($path)) {
    return $suggestions;
  }

  // Découper le chemin en segments
  $segments = explode('/', $path);

  $current_path = '';

  foreach ($segments as $index => $segment) {
    if ($index === count($segments) - 1) {
      // Dernier segment : on propose le parent
      continue;
    }

    $current_path .= '/' . $segment;

    // Vérifier si cette URL existe via get_page_by_path (plus fiable que wp_remote_head)
    $test_path = ltrim($current_path, '/');
    $page = get_page_by_path($test_path);

    if ($page) {
      $test_url = get_permalink($page->ID);
      $suggestions[] = [
        'url' => $test_url,
        'title' => ucfirst(str_replace('-', ' ', $segment)),
        'description' => "Section " . ucfirst(str_replace('-', ' ', $segment))
      ];
      continue;
    }

    // Vérifier si c'est une archive de CPT
    $post_types = get_post_types(['public' => true, '_builtin' => false], 'objects');
    foreach ($post_types as $pt) {
      if ($pt->rewrite && isset($pt->rewrite['slug']) && $pt->rewrite['slug'] === $segment) {
        $test_url = home_url($current_path);
        $suggestions[] = [
          'url' => $test_url,
          'title' => ucfirst(str_replace('-', ' ', $segment)),
          'description' => "Archive " . ucfirst(str_replace('-', ' ', $segment))
        ];
        break;
      }
    }
  }

  return $suggestions;
}

/**
 * Récupère les pages/posts populaires comme alternatives
 */
function get_popular_alternatives()
{
  $alternatives = [];

  // Pages principales
  $main_pages = get_pages(['sort_column' => 'menu_order', 'number' => 3]);
  foreach ($main_pages as $page) {
    $alternatives[] = [
      'url' => get_permalink($page->ID),
      'title' => $page->post_title,
      'description' => 'Page principale'
    ];
  }

  // Posts récents
  $recent_posts = get_posts(['numberposts' => 3, 'post_status' => 'publish']);
  foreach ($recent_posts as $post) {
    $alternatives[] = [
      'url' => get_permalink($post->ID),
      'title' => $post->post_title,
      'description' => 'Article récent'
    ];
  }

  return $alternatives;
}

$suggestions = get_smart_404_suggestions();
// var_dump($suggestions);
$alternatives = get_popular_alternatives();
?>

<div class="error-404-container">
  <div class="error-404-content">

    <!-- Message d'erreur principal -->
    <div class="error-404-header">
      <h1>🤔 Oups, la page demandée n'existe plus !</h1>
      <p>Il semblerait que cette page ait pris la poudre d'escampette... Mais ne vous inquiétez pas, nous avons quelques pistes pour vous !</p>
    </div>

    <?php if (!empty($suggestions)) : ?>
      <!-- Suggestions intelligentes -->
      <div class="error-404-suggestions">
        <h2>🎯 Vous cherchiez peut-être :</h2>
        <div class="suggestions-grid">
          <?php foreach ($suggestions as $suggestion) : ?>
            <div class="suggestion-card">
              <h3><a href="<?php echo esc_url($suggestion['url']); ?>"><?php echo esc_html($suggestion['title']); ?></a></h3>
              <p><?php echo esc_html($suggestion['description']); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($alternatives)) : ?>
      <!-- Alternatives populaires -->
      <div class="error-404-alternatives">
        <h2>🔥 Contenus populaires :</h2>
        <div class="alternatives-list">
          <?php foreach (array_slice($alternatives, 0, 6) as $alternative) : ?>
            <div class="alternative-item">
              <a href="<?php echo esc_url($alternative['url']); ?>">
                <strong><?php echo esc_html($alternative['title']); ?></strong>
                <span class="alt-description"><?php echo esc_html($alternative['description']); ?></span>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <div class="error-404-search">
      <h2>🔍 Ou faites une recherche :</h2>
      <?php get_search_form(); ?>
    </div>

    <!-- Lien vers l'accueil -->
    <div class="error-404-home">
      <a href="<?php echo home_url(); ?>" class="btn-webnetwork btn-secondaire">🏠 Retour à l'accueil</a>
    </div>

  </div>
</div>



<?php get_footer(); ?>