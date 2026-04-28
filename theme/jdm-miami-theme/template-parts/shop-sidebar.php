<?php
/**
 * Shop sidebar — AutoZone-style filters.
 *
 * Renders:
 *  - Category tree (product_cat)
 *  - Layered nav widgets for Make / Model / Year / Engine / Transmission / Condition
 *  - Price range slider
 *  - "Clear all filters" link when any filter is active
 *
 * Uses WooCommerce's built-in widgets so filtering "just works" with
 * standard URL query params (?filter_make=honda, ?min_price=0, etc.).
 *
 * @package JDM_Miami
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Detect any active filters in the current URL so we can show
 * a "Clear filters" link at the top of the sidebar.
 */
$active_filters     = array();
$known_filter_params = array(
	'filter_make',
	'filter_model',
	'filter_vehicle_year',
	'filter_engine',
	'filter_transmission',
	'filter_condition',
	'filter_mileage',
	'filter_side',
	'min_price',
	'max_price',
	'rating_filter',
	'product_cat',
);
foreach ( $known_filter_params as $param ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! empty( $_GET[ $param ] ) ) {
		$active_filters[] = $param;
	}
}

/**
 * Layered nav attributes to render in the sidebar.
 * Title => attribute slug (without "pa_" prefix).
 */
$layered_attrs = array(
	'Make'         => 'make',
	'Model'        => 'model',
	'Year'         => 'vehicle_year',
	'Engine'       => 'engine',
	'Transmission' => 'transmission',
	'Condition'    => 'condition',
);
?>

<aside class="jdm-shop-sidebar" data-jdm-shop-sidebar aria-label="<?php esc_attr_e( 'Shop filters', 'jdm_miami' ); ?>">

	<div class="jdm-shop-sidebar__header">
		<h2 class="jdm-shop-sidebar__title">
			<?php esc_html_e( 'Filters', 'jdm_miami' ); ?>
		</h2>
		<button
			type="button"
			class="jdm-shop-sidebar__close"
			data-jdm-sidebar-close
			aria-label="<?php esc_attr_e( 'Close filters', 'jdm_miami' ); ?>"
		>
			<?php echo jdm_miami_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>

	<?php if ( ! empty( $active_filters ) ) : ?>
		<a class="jdm-filter-clear" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
			<?php echo jdm_miami_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'Clear all filters', 'jdm_miami' ); ?>
		</a>
	<?php endif; ?>

	<!-- ===== Categories ===== -->
	<div class="jdm-filter-group" data-jdm-filter-group>
		<button type="button" class="jdm-filter-toggle" data-jdm-filter-toggle aria-expanded="true">
			<span><?php esc_html_e( 'Category', 'jdm_miami' ); ?></span>
			<span class="jdm-filter-chev" aria-hidden="true"></span>
		</button>
		<div class="jdm-filter-body" data-jdm-filter-body>
			<ul class="jdm-cat-list">
				<?php
				$cat_terms = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'hide_empty' => true,
						'parent'     => 0,
						'orderby'    => 'name',
					)
				);

				if ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) ) :
					$current_cat = is_product_category() ? get_queried_object_id() : 0;

					foreach ( $cat_terms as $cat ) :
						$is_current = ( $cat->term_id === $current_cat );
						?>
						<li class="jdm-cat-item<?php echo $is_current ? ' is-current' : ''; ?>">
							<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
								<span class="jdm-cat-item__label"><?php echo esc_html( $cat->name ); ?></span>
								<span class="jdm-cat-item__count"><?php echo esc_html( (string) $cat->count ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<!-- ===== Attribute layered nav ===== -->
	<?php foreach ( $layered_attrs as $label => $attr_slug ) :
		$taxonomy = 'pa_' . $attr_slug;

		// Skip if no terms exist for this attribute.
		$term_count = wp_count_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);
		if ( is_wp_error( $term_count ) || (int) $term_count === 0 ) {
			continue;
		}
		?>
		<div class="jdm-filter-group" data-jdm-filter-group>
			<button type="button" class="jdm-filter-toggle" data-jdm-filter-toggle aria-expanded="true">
				<span><?php echo esc_html( $label ); ?></span>
				<span class="jdm-filter-chev" aria-hidden="true"></span>
			</button>
			<div class="jdm-filter-body jdm-filter-body--scroll" data-jdm-filter-body>
				<?php
				the_widget(
					'WC_Widget_Layered_Nav',
					array(
						'title'        => '',
						'attribute'    => $attr_slug,
						'display_type' => 'list',
						'query_type'   => 'and',
					),
					array(
						'before_widget' => '<div class="jdm-layered-nav">',
						'after_widget'  => '</div>',
						'before_title'  => '',
						'after_title'   => '',
					)
				);
				?>
			</div>
		</div>
	<?php endforeach; ?>

	<!-- ===== Price ===== -->
	<div class="jdm-filter-group" data-jdm-filter-group>
		<button type="button" class="jdm-filter-toggle" data-jdm-filter-toggle aria-expanded="true">
			<span><?php esc_html_e( 'Price', 'jdm_miami' ); ?></span>
			<span class="jdm-filter-chev" aria-hidden="true"></span>
		</button>
		<div class="jdm-filter-body" data-jdm-filter-body>
			<?php
			the_widget(
				'WC_Widget_Price_Filter',
				array( 'title' => '' ),
				array(
					'before_widget' => '<div class="jdm-price-filter">',
					'after_widget'  => '</div>',
					'before_title'  => '',
					'after_title'   => '',
				)
			);
			?>
		</div>
	</div>

	<!-- ===== Active filters chip list ===== -->
	<?php if ( ! empty( $active_filters ) ) : ?>
		<div class="jdm-filter-group" data-jdm-filter-group>
			<button type="button" class="jdm-filter-toggle" data-jdm-filter-toggle aria-expanded="true">
				<span><?php esc_html_e( 'Active filters', 'jdm_miami' ); ?></span>
				<span class="jdm-filter-chev" aria-hidden="true"></span>
			</button>
			<div class="jdm-filter-body" data-jdm-filter-body>
				<?php
				the_widget(
					'WC_Widget_Layered_Nav_Filters',
					array( 'title' => '' ),
					array(
						'before_widget' => '<div class="jdm-active-filters">',
						'after_widget'  => '</div>',
						'before_title'  => '',
						'after_title'   => '',
					)
				);
				?>
			</div>
		</div>
	<?php endif; ?>

</aside>
