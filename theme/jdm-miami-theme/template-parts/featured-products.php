<?php
/**
 * Featured products grid on the homepage.
 *
 * @package JDM_Miami
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}
?>

<section class="jdm-section">
	<div class="jdm-container">
		<div class="jdm-section-header">
			<div>
				<span class="jdm-eyebrow"><?php esc_html_e( 'Latest Arrivals', 'jdm_miami' ); ?></span>
				<h2 class="jdm-heading-lg" style="margin-top: 0.75rem;">
					<?php esc_html_e( 'Fresh JDM Inventory', 'jdm_miami' ); ?>
				</h2>
			</div>
			<a class="jdm-btn jdm-btn-secondary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'Shop all', 'jdm_miami' ); ?>
				<?php echo jdm_miami_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>

		<?php
		echo do_shortcode( '[products limit="8" columns="4" orderby="date" order="DESC" visibility="visible"]' );
		?>
	</div>
</section>
