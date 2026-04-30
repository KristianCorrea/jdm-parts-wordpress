<?php
/**
 * About section (standalone page block).
 *
 * @package JDM_Miami
 */

$about_image_url = get_template_directory_uri() . '/assets/image/test.jpg';
$shop_url        = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '';
if ( ! $shop_url ) {
	$shop_url = home_url( '/shop/' );
}
?>

<section class="jdm-section">
	<div class="jdm-container">
		<div style="max-width: 980px; margin-inline: auto;">
			<figure style="position: relative; margin: 0; border-radius: 20px; overflow: hidden; border: 1px solid var(--color-jdm-line); aspect-ratio: 16 / 9; background: linear-gradient(180deg, var(--color-jdm-ink) 0%, var(--color-jdm-graphite) 100%); box-shadow: 0 0 0 1px color-mix(in oklab, var(--color-jdm-magenta) 15%, transparent), 0 24px 50px -20px color-mix(in oklab, var(--color-jdm-magenta) 28%, transparent);">
				<img
					src="<?php echo esc_url( $about_image_url ); ?>"
					alt="<?php echo esc_attr__( 'JDM Miami — engines and performance parts', 'jdm_miami' ); ?>"
					width="1600"
					height="900"
					loading="lazy"
					decoding="async"
					style="width: 100%; height: 100%; object-fit: cover; display: block;"
				/>
				<span style="pointer-events: none; position: absolute; inset: 0; background: linear-gradient(180deg, transparent 55%, color-mix(in oklab, var(--color-jdm-black) 80%, transparent) 100%);"></span>
			</figure>

			<div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.85rem; margin-top: 1.25rem;">
				<div class="jdm-hero-stat">
					<div style="font-family: var(--font-display); color: #fff; font-size: 1.75rem;">500+</div>
					<div style="color: var(--color-jdm-muted); font-size: 0.8rem;"><?php esc_html_e( 'Parts available', 'jdm_miami' ); ?></div>
				</div>
				<div class="jdm-hero-stat">
					<div style="font-family: var(--font-display); color: #fff; font-size: 1.75rem;">10+</div>
					<div style="color: var(--color-jdm-muted); font-size: 0.8rem;"><?php esc_html_e( 'Years experience', 'jdm_miami' ); ?></div>
				</div>
				<div class="jdm-hero-stat">
					<div style="font-family: var(--font-display); color: #fff; font-size: 1.75rem;">48h</div>
					<div style="color: var(--color-jdm-muted); font-size: 0.8rem;"><?php esc_html_e( 'Fast shipping', 'jdm_miami' ); ?></div>
				</div>
				<div class="jdm-hero-stat">
					<div style="font-family: var(--font-display); color: #fff; font-size: 1.75rem;">100%</div>
					<div style="color: var(--color-jdm-muted); font-size: 0.8rem;"><?php esc_html_e( 'Inspected parts', 'jdm_miami' ); ?></div>
				</div>
			</div>

			<div style="margin-top: 2rem; padding: 1.4rem; border-radius: 16px; border: 1px solid var(--color-jdm-line); background: linear-gradient(180deg, var(--color-jdm-ink) 0%, var(--color-jdm-graphite) 100%);">
				<span class="jdm-eyebrow"><?php esc_html_e( 'Who We Are', 'jdm_miami' ); ?></span>
				<h2 class="jdm-heading-lg" style="margin-top: 1rem;">
					<?php esc_html_e( 'Built Around Passion.', 'jdm_miami' ); ?>
					<br />
					<span class="jdm-heading-accent"><?php esc_html_e( 'Driven by Precision.', 'jdm_miami' ); ?></span>
				</h2>
				<p style="margin-top: 1.2rem; max-width: 62ch; color: var(--color-jdm-soft); font-size: 1.05rem; line-height: 1.65;">
					<?php esc_html_e( 'We specialize in sourcing authentic JDM engines and components directly from Japan. Every part is carefully inspected, documented, and selected for enthusiasts who demand reliability and performance.', 'jdm_miami' ); ?>
				</p>
				<p style="margin-top: 0.9rem; max-width: 62ch; color: var(--color-jdm-muted); font-size: 1.02rem; line-height: 1.65;">
					<?php esc_html_e( 'From full engine swaps to rare drivetrain components, our focus is simple: deliver parts you can trust, with the transparency builders actually need.', 'jdm_miami' ); ?>
				</p>

				<div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.5rem;">
					<a class="jdm-btn jdm-btn-primary" href="<?php echo esc_url( $shop_url ); ?>">
						<?php esc_html_e( 'Browse Inventory', 'jdm_miami' ); ?>
						<?php echo jdm_miami_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<a class="jdm-btn jdm-btn-secondary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
						<?php esc_html_e( 'Contact Us', 'jdm_miami' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
