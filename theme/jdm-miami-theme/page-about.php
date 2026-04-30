<?php
/**
 * About page template.
 *
 * Loaded when a Page uses slug "about" (e.g. /about/).
 * Renders the marketing block from `template-parts/about.php`.
 *
 * @package JDM_Miami
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 0;">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/about' );
	endwhile;
	?>
</main>

<?php
get_footer();
