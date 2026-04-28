<?php
/**
 * Homepage Hero
 *
 * @package JDM_Miami
 */
?>

<section class="jdm-hero">
	<div class="jdm-container">
		<div class="jdm-hero-grid">
			<div>
				<span class="jdm-eyebrow"><?php esc_html_e( 'Imported JDM Inventory', 'jdm_miami' ); ?></span>
				<h1 class="jdm-heading-xl" style="margin-top: 1.25rem;">
					<?php esc_html_e( 'Engines, Parts,', 'jdm_miami' ); ?><br />
					<span class="jdm-heading-accent"><?php esc_html_e( 'Built for Builders.', 'jdm_miami' ); ?></span>
				</h1>
				<p style="margin-top: 1.5rem; max-width: 52ch; color: var(--color-jdm-soft); font-size: 1.05rem; line-height: 1.65;">
					<?php esc_html_e( 'Curated OEM engines, drivetrain, and hard-to-find components pulled from imported JDM clips. Inspected, cataloged, and shipped with the details enthusiasts actually need.', 'jdm_miami' ); ?>
				</p>

				<div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 2rem;">
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<a class="jdm-btn jdm-btn-primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
							<?php esc_html_e( 'Shop Inventory', 'jdm_miami' ); ?>
							<?php echo jdm_miami_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>
					<a class="jdm-btn jdm-btn-secondary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
						<?php esc_html_e( 'Request a Part', 'jdm_miami' ); ?>
					</a>
				</div>

				<div style="display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 0.75rem; margin-top: 2.5rem; max-width: 540px;">
					<div class="jdm-hero-stat">
						<div style="font-family: var(--font-display); color: #fff; font-size: 1.75rem;">500+</div>
						<div style="color: var(--color-jdm-muted); font-size: 0.8rem;"><?php esc_html_e( 'SKUs in stock', 'jdm_miami' ); ?></div>
					</div>
					<div class="jdm-hero-stat">
						<div style="font-family: var(--font-display); color: #fff; font-size: 1.75rem;">12</div>
						<div style="color: var(--color-jdm-muted); font-size: 0.8rem;"><?php esc_html_e( 'Categories', 'jdm_miami' ); ?></div>
					</div>
					<div class="jdm-hero-stat">
						<div style="font-family: var(--font-display); color: #fff; font-size: 1.75rem;">48h</div>
						<div style="color: var(--color-jdm-muted); font-size: 0.8rem;"><?php esc_html_e( 'Avg. dispatch', 'jdm_miami' ); ?></div>
					</div>
				</div>
			</div>

			<div class="jdm-hero-visual" aria-hidden="true">
				<div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
					<div style="text-align: center; padding: 2rem;">
						<div style="font-family: var(--font-mono); font-size: 0.75rem; letter-spacing: 0.3em; color: var(--color-jdm-cyan); text-transform: uppercase;">
							<?php esc_html_e( 'Spec Sheet', 'jdm_miami' ); ?>
						</div>
						<div class="jdm-wordmark" style="font-size: clamp(3rem, 7vw, 5rem); margin: 1rem 0;">
							<span>JDM</span><em>Miami</em>
						</div>
						<div style="font-family: var(--font-mono); font-size: 0.75rem; color: var(--color-jdm-muted);">
							IMPORT &middot; INSPECT &middot; INSTALL
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
