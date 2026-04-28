<?php
/**
 * The main template file
 *
 * @package JDM_Miami
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="jdm-container" style="max-width: 820px;">
		<?php
		if ( have_posts() ) :

			if ( is_home() && ! is_front_page() ) :
				?>
				<header class="jdm-section-header">
					<h1 class="jdm-heading-lg"><?php single_post_title(); ?></h1>
				</header>
				<?php
			endif;

			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			endwhile;

			the_posts_navigation();

		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</div>
</main>

<?php
get_footer();
