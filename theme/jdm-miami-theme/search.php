<?php
/**
 * The template for displaying search results pages
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
					<span class="jdm-eyebrow"><?php esc_html_e( 'Search results', 'jdm_miami' ); ?></span>
					<h1 class="jdm-heading-lg" style="margin-top: 0.5rem;">
						<?php
						printf(
							/* translators: %s: search query. */
							esc_html__( 'Results for: %s', 'jdm_miami' ),
							'<span style="color: var(--color-jdm-cyan);">' . esc_html( get_search_query() ) . '</span>'
						);
						?>
					</h1>
				</div>
			</header>

			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'search' );
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
