<?php
/**
 * Contact page — business info + Google Maps embed + part-request form.
 *
 * Handles its own POST submission. Sends the request to the site admin email
 * via wp_mail(). Protected with a nonce to prevent CSRF.
 *
 * @package JDM_Miami
 */

$sent  = false;
$error = '';

if ( isset( $_POST['jdm_contact_nonce'] ) ) {
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jdm_contact_nonce'] ) ), 'jdm_contact_form' ) ) {
		$error = __( 'Security check failed. Please refresh the page and try again.', 'jdm_miami' );
	} else {
		$name  = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$parts = sanitize_textarea_field( wp_unslash( $_POST['parts'] ?? '' ) );

		if ( empty( $name ) || empty( $email ) || empty( $parts ) ) {
			$error = __( 'Please fill in all fields.', 'jdm_miami' );
		} elseif ( ! is_email( $email ) ) {
			$error = __( 'Please enter a valid email address.', 'jdm_miami' );
		} else {
			$to      = get_option( 'admin_email' );
			$subject = sprintf( __( 'New Part Request from %s', 'jdm_miami' ), $name );
			$message = sprintf(
				"Name: %s\nEmail: %s\n\nParts requested:\n%s",
				$name,
				$email,
				$parts
			);

			$sent = wp_mail( $to, $subject, $message );

			if ( ! $sent ) {
				$error = __( 'Sorry, your message could not be sent. Please try again later.', 'jdm_miami' );
			}
		}
	}
}
?>

<section class="jdm-section jdm-contact-section">
	<div class="jdm-container">

		<!-- ===== Page heading ===== -->
		<div class="jdm-contact-heading">
			<span class="jdm-eyebrow"><?php esc_html_e( 'Get in touch', 'jdm_miami' ); ?></span>
			<h1 class="jdm-heading-xl" style="margin-top: 0.75rem;">
				<?php esc_html_e( 'Contact Us', 'jdm_miami' ); ?>
			</h1>
		</div>

		<!-- ===== Two-column layout ===== -->
		<div class="jdm-contact-grid">

			<!-- ── LEFT: info + map ── -->
			<div class="jdm-contact-info">

				<!-- Phone -->
				<div class="jdm-contact-detail">
					<span class="jdm-contact-detail__icon" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.86 19.86 0 0 1 3.08 4.18 2 2 0 0 1 5.09 2h3a2 2 0 0 1 2 1.72 12.05 12.05 0 0 0 .66 2.68 2 2 0 0 1-.45 2.11L9.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.05 12.05 0 0 0 2.68.66A2 2 0 0 1 22 16.92z"/></svg>
					</span>
					<div>
						<p class="jdm-contact-detail__label"><?php esc_html_e( 'Phone', 'jdm_miami' ); ?></p>
						<a class="jdm-contact-detail__value" href="tel:+13056065533">305-606-5533</a>
					</div>
				</div>

				<!-- Email -->
				<div class="jdm-contact-detail">
					<span class="jdm-contact-detail__icon" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
					</span>
					<div>
						<p class="jdm-contact-detail__label"><?php esc_html_e( 'Email', 'jdm_miami' ); ?></p>
						<a class="jdm-contact-detail__value" href="mailto:jdmofmiami@gmail.com">jdmofmiami@gmail.com</a>
					</div>
				</div>

				<!-- Location -->
				<div class="jdm-contact-detail">
					<span class="jdm-contact-detail__icon" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
					</span>
					<div>
						<p class="jdm-contact-detail__label"><?php esc_html_e( 'Location', 'jdm_miami' ); ?></p>
						<address class="jdm-contact-detail__value jdm-contact-address">
							14195 SW 139th Ct Bay 2<br>
							Miami, FL 33186
						</address>
					</div>
				</div>

				<!-- Hours -->
				<div class="jdm-contact-detail">
					<span class="jdm-contact-detail__icon" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
					</span>
					<div>
						<p class="jdm-contact-detail__label"><?php esc_html_e( 'Hours', 'jdm_miami' ); ?></p>
						<ul class="jdm-contact-hours">
							<li><span><?php esc_html_e( 'Mon – Fri', 'jdm_miami' ); ?></span><span>10 AM – 5 PM</span></li>
							<li><span><?php esc_html_e( 'Saturday', 'jdm_miami' ); ?></span><span><?php esc_html_e( 'By appointment only', 'jdm_miami' ); ?></span></li>
							<li><span><?php esc_html_e( 'Sunday', 'jdm_miami' ); ?></span><span><?php esc_html_e( 'Closed', 'jdm_miami' ); ?></span></li>
						</ul>
					</div>
				</div>

				<!-- Google Maps embed -->
				<div class="jdm-contact-map">
					<iframe
						title="<?php esc_attr_e( 'JDM Miami store location', 'jdm_miami' ); ?>"
						src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14388.2907701025!2d-80.4235269!3d25.6357128!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88d9c18646a5bf4d%3A0xccd8d76fd00832ac!2sJDM%20of%20Miami%20LLC!5e0!3m2!1sen!2sus!4v1777509531367!5m2!1sen!2sus"
						width="100%"
						height="260"
						style="border: 0;"
						allowfullscreen=""
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
					></iframe>
				</div>

			</div><!-- /.jdm-contact-info -->

			<!-- ── RIGHT: form ── -->
			<div class="jdm-contact-form-col">
				<p class="jdm-contact-form-intro">
					<?php esc_html_e( "You may also email us using the form below and we'll get back to you.", 'jdm_miami' ); ?>
				</p>

				<?php if ( $sent ) : ?>
					<div class="jdm-contact-success">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
						<?php esc_html_e( "Your request was sent! We'll be in touch soon.", 'jdm_miami' ); ?>
					</div>
				<?php else : ?>

					<?php if ( $error ) : ?>
						<div class="jdm-contact-error"><?php echo esc_html( $error ); ?></div>
					<?php endif; ?>

					<form class="jdm-contact-form" method="post">
						<?php wp_nonce_field( 'jdm_contact_form', 'jdm_contact_nonce' ); ?>

						<div class="jdm-contact-field">
							<label class="jdm-contact-label" for="jdm-name">
								<?php esc_html_e( 'Full Name', 'jdm_miami' ); ?>
							</label>
							<input
								id="jdm-name"
								class="jdm-contact-input"
								type="text"
								name="name"
								autocomplete="name"
								required
								value="<?php echo isset( $_POST['name'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['name'] ) ) ) : ''; ?>"
							>
						</div>

						<div class="jdm-contact-field">
							<label class="jdm-contact-label" for="jdm-email">
								<?php esc_html_e( 'Email Address', 'jdm_miami' ); ?>
							</label>
							<input
								id="jdm-email"
								class="jdm-contact-input"
								type="email"
								name="email"
								autocomplete="email"
								required
								value="<?php echo isset( $_POST['email'] ) ? esc_attr( sanitize_email( wp_unslash( $_POST['email'] ) ) ) : ''; ?>"
							>
						</div>

						<div class="jdm-contact-field">
							<label class="jdm-contact-label" for="jdm-parts">
								<?php esc_html_e( "Message", 'jdm_miami' ); ?>
							</label>
							<textarea
								id="jdm-parts"
								class="jdm-contact-input jdm-contact-textarea"
								name="parts"
								rows="6"
								required
								placeholder="<?php esc_attr_e( 'e.g. I need a RB26DETT engine, SR20DET swap kit, ECU…', 'jdm_miami' ); ?>"
							><?php echo isset( $_POST['parts'] ) ? esc_textarea( sanitize_textarea_field( wp_unslash( $_POST['parts'] ) ) ) : ''; ?></textarea>
						</div>

						<button type="submit" class="jdm-btn jdm-btn-primary jdm-contact-submit">
							<?php esc_html_e( 'Submit Request', 'jdm_miami' ); ?>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
						</button>
					</form>

				<?php endif; ?>
			</div><!-- /.jdm-contact-form-col -->

		</div><!-- /.jdm-contact-grid -->
	</div>
</section>
