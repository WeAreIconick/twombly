<?php
/**
 * Title: Colophon
 * Slug: twombly/colophon
 * Description: A pattern providing the backlink to WordPress.org
 * Inserter: no
 */
?>

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">
	<?php
	printf(
		/* translators: Powered by WordPress. %s: WordPress link. */
		esc_html__( 'Powered by %s', 'twombly' ),
		'<a href="' . esc_url( __( 'https://wordpress.org', 'twombly' ) ) . '" rel="nofollow">WordPress</a>'
	);
	?>
</p>
<!-- /wp:paragraph -->
