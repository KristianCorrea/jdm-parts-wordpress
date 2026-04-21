<?php
/**
 * The template for displaying archive pages
 *
 * @package JDM_Miami
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="jdm-container" style="max-width: 1100px;">
		<?php if ( have_posts() ) : ?>

			<header class="jdm-section-header">
				<div>
					<span class="jdm-eyebrow"><?php esc_html_e( 'Archive', 'jdm_miami' ); ?></span>
					<?php the_archive_title( '<h1 class="jdm-heading-lg" style="margin-top: 0.5rem;">', '</h1>' ); ?>
					<?php the_archive_description( '<div class="archive-description" style="margin-top: 0.75rem; color: var(--color-jdm-muted);">', '</div>' ); ?>
				</div>
			</header>

			<?php
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
