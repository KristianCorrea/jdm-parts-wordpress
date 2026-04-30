<?php
/**
 * Template for the Contact page.
 *
 * WordPress automatically uses this file for any page whose slug is "contact".
 * Create a page in WP Admin → Pages → Add New with slug "contact" and no body
 * content; this template handles everything.
 *
 * @package JDM_Miami
 */

get_header();
?>

<main id="primary" class="site-main">
	<?php get_template_part( 'template-parts/contact-form' ); ?>
</main>

<?php
get_footer();
