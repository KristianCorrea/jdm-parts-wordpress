<?php
/**
 * The header for our theme
 *
 * @package JDM_Miami
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'jdm_miami' ); ?></a>

<div id="page" class="site">
	<header id="masthead" class="jdm-header">
		<div class="jdm-container">
			<div class="jdm-header-inner">
				<div class="jdm-brand">
					<?php jdm_miami_brand(); ?>
				</div>

				<nav class="jdm-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'jdm_miami' ); ?>">
					<?php
					if ( has_nav_menu( 'menu-1' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'container'      => false,
								'depth'          => 1,
							)
						);
					} else {
						echo '<ul>';
						echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
						if ( class_exists( 'WooCommerce' ) ) {
							echo '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Shop</a></li>';
						}
						echo '<li><a href="' . esc_url( jdm_miami_about_page_url() ) . '">About</a></li>';
						echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">Contact</a></li>';
						echo '</ul>';
					}
					?>
				</nav>

				<div class="jdm-header-actions">
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<a class="jdm-icon-btn" href="<?php echo esc_url( get_search_link() ? home_url( '/?s=' ) : home_url( '/?s=' ) ); ?>" aria-label="<?php esc_attr_e( 'Search', 'jdm_miami' ); ?>">
							<?php echo jdm_miami_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>

						<a class="jdm-icon-btn" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'My Account', 'jdm_miami' ); ?>">
							<?php echo jdm_miami_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>

						<a class="jdm-icon-btn jdm-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'View cart', 'jdm_miami' ); ?>">
							<?php echo jdm_miami_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="jdm-cart-count" data-jdm-cart-count><?php echo esc_html( jdm_miami_cart_count() ); ?></span>
						</a>
					<?php endif; ?>

					<button
						class="jdm-menu-toggle"
						type="button"
						aria-controls="jdm-mobile-panel"
						aria-expanded="false"
						data-jdm-menu-toggle
					>
						<span data-jdm-menu-icon="open"><?php echo jdm_miami_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span data-jdm-menu-icon="close" style="display:none;"><?php echo jdm_miami_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'jdm_miami' ); ?></span>
					</button>
				</div>
			</div>

			<div id="jdm-mobile-panel" class="jdm-mobile-panel" data-jdm-mobile-panel>
				<?php
				if ( has_nav_menu( 'menu-1' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'menu-1',
							'menu_id'        => 'primary-menu-mobile',
							'container'      => false,
							'depth'          => 1,
						)
					);
				}
				?>
			</div>
		</div>
	</header>

<script>
	(function () {
		var btn = document.querySelector('[data-jdm-menu-toggle]');
		var panel = document.querySelector('[data-jdm-mobile-panel]');
		var iconOpen = document.querySelector('[data-jdm-menu-icon="open"]');
		var iconClose = document.querySelector('[data-jdm-menu-icon="close"]');
		if (!btn || !panel) { return; }
		btn.addEventListener('click', function () {
			var isOpen = panel.classList.toggle('is-open');
			btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			if (iconOpen && iconClose) {
				iconOpen.style.display = isOpen ? 'none' : '';
				iconClose.style.display = isOpen ? '' : 'none';
			}
		});
	})();
</script>
