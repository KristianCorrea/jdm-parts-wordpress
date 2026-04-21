<?php
/**
 * The template for displaying all pages
 *
 * @package JDM_Miami
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="jdm-container" style="max-width: 820px;">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
