<?php
/**
 * Service excerpt override: render the full, filtered description.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$service_post = get_post();

if ( ! $service_post ) {
	return;
}

?>
<div class="entry-content mpa-service-full-content">
	<?php echo apply_filters( 'the_content', $service_post->post_content ); ?>
</div>