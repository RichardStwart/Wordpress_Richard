<?php
/**
 * Displays header image
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<div class="custom-header">

	<?php if ( has_custom_header() ) : ?>
		<div class="custom-header-image">
			<?php the_custom_header_markup(); ?>
		</div>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>

</div><!-- .custom-header -->
