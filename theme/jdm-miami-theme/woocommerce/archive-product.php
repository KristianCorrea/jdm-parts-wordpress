<?php
/**
 * Product archive (Shop, product categories, product attributes).
 *
 * Theme override of WooCommerce's archive-product.php.
 * Wraps the loop in a two-column layout with an AutoZone-style
 * filter sidebar on the left and the product grid on the right.
 *
 * @package JDM_Miami
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked jdm_miami_woocommerce_wrapper_before - 10  (opens main + .jdm-container)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action( 'woocommerce_before_main_content' );
?>

<div class="jdm-shop">

	<header class="jdm-shop-header">
		<?php
		/**
		 * Hook: woocommerce_shop_loop_header.
		 *
		 * @since 8.6.0
		 * @hooked woocommerce_product_taxonomy_archive_header - 10
		 *
		 * This hook renders the archive title + description.
		 * We do NOT add a separate <h1> or woocommerce_archive_description
		 * call below — both would duplicate the output.
		 */
		do_action( 'woocommerce_shop_loop_header' );
		?>
	</header>

	<div class="jdm-shop-layout">

		<?php get_template_part( 'template-parts/shop-sidebar' ); ?>

		<div class="jdm-shop-main">

			<div class="jdm-shop-toolbar">
				<button
					type="button"
					class="jdm-btn jdm-btn-secondary jdm-shop-filter-trigger"
					data-jdm-sidebar-open
					aria-controls="jdm-shop-sidebar-overlay"
				>
					<?php echo jdm_miami_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Filters', 'jdm_miami' ); ?>
				</button>

				<?php
				if ( woocommerce_product_loop() ) {
					/**
					 * Hook: woocommerce_before_shop_loop.
					 *
					 * @hooked woocommerce_output_all_notices - 10
					 * @hooked woocommerce_result_count - 20
					 * @hooked woocommerce_catalog_ordering - 30
					 */
					do_action( 'woocommerce_before_shop_loop' );
				}
				?>
			</div>

			<?php
			if ( woocommerce_product_loop() ) {

				woocommerce_product_loop_start();

				if ( wc_get_loop_prop( 'total' ) ) {
					while ( have_posts() ) {
						the_post();

						/**
						 * Hook: woocommerce_shop_loop.
						 */
						do_action( 'woocommerce_shop_loop' );

						wc_get_template_part( 'content', 'product' );
					}
				}

				woocommerce_product_loop_end();

				/**
				 * Hook: woocommerce_after_shop_loop.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );

			} else {
				/**
				 * Hook: woocommerce_no_products_found.
				 *
				 * @hooked wc_no_products_found - 10
				 */
				do_action( 'woocommerce_no_products_found' );
			}
			?>
		</div><!-- .jdm-shop-main -->
	</div><!-- .jdm-shop-layout -->
</div><!-- .jdm-shop -->

<!-- Mobile sidebar scrim -->
<div class="jdm-shop-sidebar-scrim" data-jdm-sidebar-scrim aria-hidden="true"></div>

<script>
(function () {
	var sidebar = document.querySelector('[data-jdm-shop-sidebar]');
	var scrim   = document.querySelector('[data-jdm-sidebar-scrim]');
	var openers = document.querySelectorAll('[data-jdm-sidebar-open]');
	var closers = document.querySelectorAll('[data-jdm-sidebar-close]');
	if (!sidebar) { return; }

	function open()  {
		document.body.classList.add('jdm-shop-sidebar-open');
	}
	function close() {
		document.body.classList.remove('jdm-shop-sidebar-open');
	}

	openers.forEach(function (b) { b.addEventListener('click', open); });
	closers.forEach(function (b) { b.addEventListener('click', close); });
	if (scrim) { scrim.addEventListener('click', close); }

	// Filter group accordion toggles.
	document.querySelectorAll('[data-jdm-filter-toggle]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var group = btn.closest('[data-jdm-filter-group]');
			var body  = group ? group.querySelector('[data-jdm-filter-body]') : null;
			if (!body) { return; }
			var isOpen = group.classList.toggle('is-collapsed');
			btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
		});
	});

	// Close drawer on Escape.
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { close(); }
	});
})();
</script>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked jdm_miami_woocommerce_wrapper_after - 10  (closes .jdm-container + main)
 */
do_action( 'woocommerce_after_main_content' );

// Skip the default sidebar — we render our own filters.

get_footer( 'shop' );
