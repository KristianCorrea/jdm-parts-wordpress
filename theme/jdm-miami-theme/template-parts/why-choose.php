<?php
/**
 * "Why choose JDM Miami" feature section.
 *
 * @package JDM_Miami
 */

$features = array(
	array(
		'icon'  => 'engine',
		'title' => __( 'Imported JDM Sourcing', 'jdm_miami' ),
		'desc'  => __( 'Engines, drivetrain, and components pulled from Japanese-market donor vehicles — low mileage and OEM.', 'jdm_miami' ),
	),
	array(
		'icon'  => 'shield',
		'title' => __( 'Inspected & Cataloged', 'jdm_miami' ),
		'desc'  => __( 'Every unit is visually inspected, tested where possible, and listed with the fitment details that matter.', 'jdm_miami' ),
	),
	array(
		'icon'  => 'truck',
		'title' => __( 'Fast Fulfilment', 'jdm_miami' ),
		'desc'  => __( 'In-stock parts ship in 48 hours. Large engine units crated and freighted nationwide.', 'jdm_miami' ),
	),
	array(
		'icon'  => 'tools',
		'title' => __( 'Built for Builders', 'jdm_miami' ),
		'desc'  => __( 'Talk shop with people who actually wrench. We source for enthusiasts, not generic catalog buyers.', 'jdm_miami' ),
	),
);
?>

<section class="jdm-section">
	<div class="jdm-container">
		<div class="jdm-section-header">
			<div>
				<span class="jdm-eyebrow"><?php esc_html_e( 'Why JDM Miami', 'jdm_miami' ); ?></span>
				<h2 class="jdm-heading-lg" style="margin-top: 0.75rem;">
					<?php esc_html_e( 'Not Another Parts Catalog.', 'jdm_miami' ); ?>
				</h2>
			</div>
		</div>

		<div class="jdm-feature-grid">
			<?php foreach ( $features as $feature ) : ?>
				<div class="jdm-feature">
					<div class="jdm-feature-icon">
						<?php echo jdm_miami_icon( $feature['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<h3><?php echo esc_html( $feature['title'] ); ?></h3>
					<p><?php echo esc_html( $feature['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
