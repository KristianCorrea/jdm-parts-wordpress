<?php
/**
 * Shop by Make — horizontal brand strip on the homepage.
 *
 * Fetches every pa_make term (hide_empty: false so zero-product makes
 * still appear), and links each card to the shop filtered by that make
 * using WooCommerce's layered-nav query-var format:
 *   /?post_type=product&filter_make={slug}
 *
 * @package JDM_Miami
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$makes = get_terms(
	array(
		'taxonomy'   => 'pa_make',
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

if ( empty( $makes ) || is_wp_error( $makes ) ) {
	return;
}
?>

<section class="jdm-section jdm-makes-section">
	<div class="jdm-container">
		<div class="jdm-section-header">
			<div>
				<span class="jdm-eyebrow"><?php esc_html_e( 'Filter by manufacturer', 'jdm_miami' ); ?></span>
				<h2 class="jdm-heading-lg" style="margin-top: 0.75rem;">
					<?php esc_html_e( 'Shop by Make', 'jdm_miami' ); ?>
				</h2>
			</div>
			<a class="jdm-btn jdm-btn-ghost" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'View all parts', 'jdm_miami' ); ?>
				<?php echo jdm_miami_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>

		<div class="jdm-makes-strip">
			<?php foreach ( $makes as $make ) :
				$filter_url = add_query_arg(
					array(
						'post_type'   => 'product',
						'filter_make' => $make->slug,
					),
					home_url( '/' )
				);
				$count_label = $make->count > 0
					? sprintf( _n( '%s part', '%s parts', $make->count, 'jdm_miami' ), number_format_i18n( $make->count ) )
					: __( 'Coming soon', 'jdm_miami' );
				$logo_placeholder = strtoupper( substr( sanitize_text_field( $make->name ), 0, 2 ) );
			?>
				<a
					class="jdm-makes-card<?php echo 0 === $make->count ? ' jdm-makes-card--empty' : ''; ?>"
					href="<?php echo esc_url( $filter_url ); ?>"
				>
					<span class="jdm-makes-card__logo" aria-hidden="true"><?php echo esc_html( $logo_placeholder ); ?></span>
					<span class="jdm-makes-card__name"><?php echo esc_html( $make->name ); ?></span>
					<span class="jdm-makes-card__count"><?php echo esc_html( $count_label ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
		<p class="jdm-makes-scroll-indicator">
			<?php esc_html_e( 'Scroll for more makes', 'jdm_miami' ); ?>
			<span aria-hidden="true">→</span>
		</p>
	</div>
</section>
