<?php
if (!isset($args['btn']) || empty($args['btn'])) {
  return;
}

$btn = $args['btn'];

$libelle = $btn['libelle_du_bouton'] ?? '';
$link = $btn['lien_du_bouton'] ?? '';
$class = $btn['classe_du_bouton'] ?? '';
$id = $btn['id_du_bouton'] ?? '';
$parametre = $btn['parametre_du_bouton'] ?? '';
$type = $btn['type_de_bouton'] ?? 'btn-primaire';
$ouvrir_nouvel_onglet = $btn['ouvrir_dans_un_nouvel_onglet'] ?? false;
$icone_target = '<span><img src=' . render_media('transverse/icone_lien-externe.svg') . ' alt=""></span>';

if ($link != '#') {
  $link = home_url($link);
}

if (empty($libelle) || empty($link)) {
  return;
}
?>
<a href="<?php echo esc_url($link); ?>" class="btn-galaxyy <?php echo esc_attr($type); ?> <?php echo esc_attr($class); ?>" <?php if ($id) { ?> id="<?php echo esc_attr($id); ?>" <?php } ?> <?php if ($ouvrir_nouvel_onglet) { ?> target="_blank" rel="noopener noreferrer" <?php } ?> <?php if ($parametre) { ?> data-ancre="<?php echo esc_attr($parametre); ?>" <?php } ?>><?php echo esc_html($libelle); ?> <?php if ($ouvrir_nouvel_onglet) { ?> <?php echo $icone_target; ?> <?php } ?></a>