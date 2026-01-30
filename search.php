<?php

get_header();
?>

<style>
  /* Conteneur principal */
  .search-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
  }

  /* En-tête de recherche */
  .search-header {
    margin-bottom: 40px;
    text-align: center;
  }

  .search-header h1 {
    font-size: 2em;
    margin-bottom: 10px;
    color: #333;
  }

  .search-query {
    color: #0073aa;
    font-weight: bold;
  }

  .search-count {
    color: #666;
    font-size: 0.9em;
  }

  /* Formulaire de recherche */
  .search-form-container {
    max-width: 600px;
    margin: 0 auto 40px;
  }

  .search-form {
    display: flex;
    gap: 10px;
  }

  .search-form input[type="search"] {
    flex: 1;
    padding: 12px 20px;
    font-size: 16px;
    border: 2px solid #ddd;
    border-radius: 4px;
    transition: border-color 0.3s;
  }

  .search-form input[type="search"]:focus {
    outline: none;
    border-color: #0073aa;
  }

  .search-form button {
    padding: 12px 30px;
    background: #0073aa;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s;
  }

  .search-form button:hover {
    background: #005a87;
  }

  /* Résultats */
  .search-results {
    display: grid;
    gap: 30px;
  }

  .search-result-item {
    padding: 20px;
    background: #f9f9f9;
    border-left: 4px solid #0073aa;
    border-radius: 4px;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .search-result-item:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .search-result-title {
    margin: 0 0 10px 0;
  }

  .search-result-title a {
    color: #0073aa;
    text-decoration: none;
    font-size: 1.5em;
    font-weight: 600;
  }

  .search-result-title a:hover {
    text-decoration: underline;
  }

  .search-result-meta {
    color: #666;
    font-size: 0.85em;
    margin-bottom: 10px;
  }

  .search-result-excerpt {
    color: #333;
    line-height: 1.6;
  }

  .search-result-excerpt mark {
    background: #fff59d;
    padding: 2px 4px;
    font-weight: 600;
  }

  /* Pagination */
  .search-pagination {
    margin-top: 50px;
    text-align: center;
  }

  .pagination {
    display: inline-flex;
    gap: 10px;
    list-style: none;
    padding: 0;
  }

  .pagination a,
  .pagination span {
    display: block;
    padding: 10px 15px;
    background: #f0f0f0;
    color: #333;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.3s;
  }

  .pagination a:hover {
    background: #0073aa;
    color: white;
  }

  .pagination .current {
    background: #0073aa;
    color: white;
  }

  /* Message vide */
  .no-results {
    text-align: center;
    padding: 60px 20px;
  }

  .no-results h2 {
    color: #666;
    font-size: 1.5em;
    margin-bottom: 20px;
  }

  .no-results p {
    color: #999;
    margin-bottom: 30px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .search-page {
      padding: 20px 15px;
    }

    .search-header h1 {
      font-size: 1.5em;
    }

    .search-form {
      flex-direction: column;
    }

    .search-form button {
      width: 100%;
    }
  }
</style>

<div class="search-page">
  <div class="search-header">
    <h1>
      <?php if (have_posts()) : ?>
        Résultats pour : <span class="search-query">"<?php echo get_search_query(); ?>"</span>
      <?php else : ?>
        Aucun résultat pour : <span class="search-query">"<?php echo get_search_query(); ?>"</span>
      <?php endif; ?>
    </h1>
    <?php if (have_posts()) : ?>
      <p class="search-count">
        <?php
        global $wp_query;
        echo $wp_query->found_posts . ' résultat(s) trouvé(s)';
        ?>
      </p>
    <?php endif; ?>
  </div>

  <div class="search-form-container">
    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
      <input type="search"
        name="s"
        placeholder="Rechercher..."
        value="<?php echo get_search_query(); ?>"
        required>
      <button type="submit">Rechercher</button>
    </form>
  </div>

  <?php if (have_posts()) : ?>
    <div class="search-results">
      <?php while (have_posts()) : the_post(); ?>
        <article class="search-result-item">
          <h2 class="search-result-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>

          <div class="search-result-meta">
            Par <?php the_author(); ?> |
            <?php echo get_the_date(); ?> |
            <?php echo get_post_type_object(get_post_type())->labels->singular_name; ?>
          </div>

          <div class="search-result-excerpt">
            <?php
            // Affiche l'extrait avec les mots recherchés en surbrillance
            $excerpt = get_the_excerpt();
            $search_query = get_search_query();
            if ($search_query) {
              $excerpt = preg_replace(
                '/(' . preg_quote($search_query, '/') . ')/i',
                '<mark>$1</mark>',
                $excerpt
              );
            }
            echo $excerpt;
            ?>
          </div>
        </article>
      <?php endwhile; ?>
    </div>

    <div class="search-pagination">
      <?php
      // Pagination avec numéros de page
      echo paginate_links(array(
        'prev_text' => '&laquo; Précédent',
        'next_text' => 'Suivant &raquo;',
        'type' => 'list',
      ));
      ?>
    </div>

  <?php else : ?>
    <div class="no-results">
      <h2>Aucun résultat trouvé</h2>
      <p>Essayez avec des mots-clés différents ou plus généraux.</p>
    </div>
  <?php endif; ?>
</div>

<script>
  // Met le focus sur le champ de recherche au chargement
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-form input[type="search"]');
    if (searchInput && searchInput.value === '') {
      searchInput.focus();
    }

    // Animation smooth au scroll vers les résultats
    const resultItems = document.querySelectorAll('.search-result-item');
    resultItems.forEach((item, index) => {
      item.style.opacity = '0';
      item.style.transform = 'translateY(20px)';

      setTimeout(() => {
        item.style.transition = 'opacity 0.5s, transform 0.5s';
        item.style.opacity = '1';
        item.style.transform = 'translateY(0)';
      }, index * 100);
    });
  });
</script>

<?php get_footer(); ?>