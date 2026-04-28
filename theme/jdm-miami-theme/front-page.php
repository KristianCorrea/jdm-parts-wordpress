<?php
/**
 * Front page template
 *
 * Loaded automatically by WordPress when a static front page is set,
 * or whenever Settings > Reading is "Your latest posts" AND the theme
 * chooses front-page.php over index.php for the home route.
 *
 * @package JDM_Miami
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 0;">
	<?php
	get_template_part( 'template-parts/hero' );
	get_template_part( 'template-parts/featured-categories' );
	get_template_part( 'template-parts/featured-makes' );
	get_template_part( 'template-parts/featured-products' );
	get_template_part( 'template-parts/why-choose' );
	get_template_part( 'template-parts/cta-band' );
	?>
</main>

<?php
get_footer();
