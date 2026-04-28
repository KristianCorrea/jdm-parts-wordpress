<?php
/**
 * The template for displaying 404 pages
 *
 * @package JDM_Miami
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="jdm-container" style="max-width: 720px; text-align: center; padding: 4rem 1.25rem;">
		<span class="jdm-eyebrow" style="justify-content: center;"><?php esc_html_e( 'Error 404', 'jdm_miami' ); ?></span>
		<h1 class="jdm-heading-xl" style="margin: 1rem 0 1rem;">
			<?php esc_html_e( 'That part is missing.', 'jdm_miami' ); ?>
		</h1>
		<p style="color: var(--color-jdm-soft); max-width: 52ch; margin: 0 auto 2rem;">
			<?php esc_html_e( 'The page you requested could not be found. Try the shop or head back home.', 'jdm_miami' ); ?>
		</p>
		<div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
			<a class="jdm-btn jdm-btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Back home', 'jdm_miami' ); ?>
			</a>
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<a class="jdm-btn jdm-btn-secondary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
					<?php esc_html_e( 'Browse inventory', 'jdm_miami' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php
get_footer();
