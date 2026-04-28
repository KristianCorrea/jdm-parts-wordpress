<?php
/**
 * The template for displaying the footer
 *
 * @package JDM_Miami
 */
?>

	<footer id="colophon" class="jdm-footer">
		<div class="jdm-container">
			<div class="jdm-footer-grid">
				<div>
					<div style="margin-bottom: 1rem;">
						<?php jdm_miami_brand(); ?>
					</div>
					<p style="color: var(--color-jdm-muted); max-width: 28ch; font-size: 0.92rem; line-height: 1.65;">
						<?php esc_html_e( 'Imported JDM engines, drivetrain, and rare OEM parts. Sourced for builders, tuners, and enthusiasts who expect better.', 'jdm_miami' ); ?>
					</p>
				</div>

				<div>
					<h4><?php esc_html_e( 'Shop', 'jdm_miami' ); ?></h4>
					<?php
					if ( has_nav_menu( 'footer-shop' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer-shop',
								'container'      => false,
								'depth'          => 1,
							)
						);
					} elseif ( class_exists( 'WooCommerce' ) ) {
						$shop_url = wc_get_page_permalink( 'shop' );
						?>
						<ul>
							<li><a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'All Inventory', 'jdm_miami' ); ?></a></li>
							<li><a href="<?php echo esc_url( jdm_miami_term_url( 'engine-parts', 'product_cat', $shop_url ) ); ?>"><?php esc_html_e( 'Engines & Engine Parts', 'jdm_miami' ); ?></a></li>
							<li><a href="<?php echo esc_url( jdm_miami_term_url( 'transmissions-drivetrain', 'product_cat', $shop_url ) ); ?>"><?php esc_html_e( 'Transmissions & Drivetrain', 'jdm_miami' ); ?></a></li>
							<li><a href="<?php echo esc_url( jdm_miami_term_url( 'ecu-modules', 'product_cat', $shop_url ) ); ?>"><?php esc_html_e( 'ECU & Modules', 'jdm_miami' ); ?></a></li>
						</ul>
						<?php
					}
					?>
				</div>

				<div>
					<h4><?php esc_html_e( 'Company', 'jdm_miami' ); ?></h4>
					<?php
					if ( has_nav_menu( 'footer-info' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer-info',
								'container'      => false,
								'depth'          => 1,
							)
						);
					} else {
						?>
						<ul>
							<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'jdm_miami' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'jdm_miami' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/shipping/' ) ); ?>"><?php esc_html_e( 'Shipping & Returns', 'jdm_miami' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/warranty/' ) ); ?>"><?php esc_html_e( 'Warranty', 'jdm_miami' ); ?></a></li>
						</ul>
						<?php
					}
					?>
				</div>

				<div>
					<h4><?php esc_html_e( 'Contact', 'jdm_miami' ); ?></h4>
					<ul>
						<li><span style="color: var(--color-jdm-muted);"><?php esc_html_e( 'Miami, FL', 'jdm_miami' ); ?></span></li>
						<li><a href="mailto:hello@jdmmiami.com">hello@jdmmiami.com</a></li>
						<li><a href="tel:+13055550123">+1 (305) 555-0123</a></li>
						<li><span class="jdm-badge jdm-badge-neon" style="margin-top: 0.5rem;"><?php esc_html_e( 'Open Mon - Sat', 'jdm_miami' ); ?></span></li>
					</ul>
				</div>
			</div>

			<div class="jdm-footer-bottom">
				<span>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> JDM Miami. <?php esc_html_e( 'All rights reserved.', 'jdm_miami' ); ?></span>
				<span><?php esc_html_e( 'Built for enthusiasts.', 'jdm_miami' ); ?></span>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
