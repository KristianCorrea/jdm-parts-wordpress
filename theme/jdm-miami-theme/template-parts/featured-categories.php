<?php
/**
 * Featured categories (product categories tile grid).
 *
 * @package JDM_Miami
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$featured_cat_slugs = array(
	'engine-parts',
	'transmissions-drivetrain',
	'suspension-steering',
	'brakes',
	'ecu-modules',
	'exterior-body',
	'wheels-tires',
	'lighting',
);

$terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'slug'       => $featured_cat_slugs,
		'hide_empty' => false,
	)
);

if ( empty( $terms ) || is_wp_error( $terms ) ) {
	return;
}
?>

<section class="jdm-section">
	<div class="jdm-container">
		<div class="jdm-section-header">
			<div>
				<span class="jdm-eyebrow"><?php esc_html_e( 'Browse by category', 'jdm_miami' ); ?></span>
				<h2 class="jdm-heading-lg" style="margin-top: 0.75rem;">
					<?php esc_html_e( 'Curated Parts Categories', 'jdm_miami' ); ?>
				</h2>
			</div>
			<a class="jdm-btn jdm-btn-ghost" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'View all', 'jdm_miami' ); ?>
				<?php echo jdm_miami_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>

		<div class="jdm-tiles">
			<?php foreach ( $terms as $term ) : ?>
				<a class="jdm-tile" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
					<span class="jdm-tile-count"><?php echo esc_html( str_pad( (string) $term->count, 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h3 class="jdm-tile-title"><?php echo esc_html( $term->name ); ?></h3>
					<p class="jdm-tile-sub">
						<?php
						echo esc_html(
							$term->description
								? wp_trim_words( $term->description, 10, '...' )
								: __( 'Shop curated inventory.', 'jdm_miami' )
						);
						?>
					</p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
