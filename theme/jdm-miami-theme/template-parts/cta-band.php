<?php
/**
 * CTA band.
 *
 * @package JDM_Miami
 */
?>

<section class="jdm-section" style="padding-top: 0;">
	<div class="jdm-container">
		<div class="jdm-cta-band">
			<div style="display: grid; gap: 2rem; grid-template-columns: 1fr; align-items: center;">
				<div>
					<span class="jdm-eyebrow"><?php esc_html_e( 'Looking for something specific?', 'jdm_miami' ); ?></span>
					<h2 class="jdm-heading-lg" style="margin-top: 0.75rem; max-width: 18ch;">
						<?php esc_html_e( 'Can&#39;t find the part? We&#39;ll source it.', 'jdm_miami' ); ?>
					</h2>
					<p style="margin-top: 1rem; max-width: 60ch; color: var(--color-jdm-soft);">
						<?php esc_html_e( 'Our import network pulls from clips across Japan every month. Tell us the year, make, model, and part — we&#39;ll tell you what&#39;s possible.', 'jdm_miami' ); ?>
					</p>
					<div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.5rem;">
						<a class="jdm-btn jdm-btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
							<?php esc_html_e( 'Request a quote', 'jdm_miami' ); ?>
							<?php echo jdm_miami_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<?php if ( class_exists( 'WooCommerce' ) ) : ?>
							<a class="jdm-btn jdm-btn-secondary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
								<?php esc_html_e( 'Browse inventory', 'jdm_miami' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
