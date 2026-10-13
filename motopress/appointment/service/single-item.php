<?php
/**
 * Accordion layout for the services list item.
 * Overrides: post/single-item.php (loaded as 'service/single-item.php')
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract(
	array(
		'template_name'   => mpa_prefix( 'post' ),
		'show_image'      => false,
		'show_title'      => false,
		'show_content'    => false,
		'show_excerpt'    => false,
		'show_attributes' => false,
		'show_extra'      => false,
	),
	EXTR_SKIP
);

$itemArgs = $template_args + array( 'view' => 'single' );

if ( post_password_required() ) {
	$show_extra = $show_attributes = $show_excerpt = $show_content = $show_title = $show_image = false;
}

$itemClass = apply_filters( "{$template_name}_item_class", mpa_get_post_class( 'mpa-grid-column tpp-accordion-item' ) );

do_action( "{$template_name}_before_item", $itemArgs );
?>
<div class="<?php echo esc_attr( $itemClass ); ?>">
	<div class="mpa-loop-post-wrapper tpp-accordion-wrapper">

		<div class="tpp-accordion-header" role="button" tabindex="0" aria-expanded="false">
			<?php
			if ( $show_image && has_post_thumbnail() ) {
				do_action( "{$template_name}_item_image", $itemArgs );
			}

			if ( $show_title ) {
				do_action( "{$template_name}_item_title", $itemArgs );
			}
			?>
			<span class="tpp-accordion-icon" aria-hidden="true"></span>
		</div>

		<div class="tpp-accordion-panel" hidden>
			<?php
			do_action( "{$template_name}_after_item_start", $itemArgs );

			if ( $show_attributes ) {
				do_action( "{$template_name}_item_attributes", $itemArgs );
			}

			if ( $show_extra ) {
				do_action( "{$template_name}_item_extra", $itemArgs );
			}

			if ( $show_content ) {
				do_action( "{$template_name}_item_content", $itemArgs );
			}

			if ( $show_excerpt ) {
				do_action( "{$template_name}_item_excerpt", $itemArgs );
			}

			// See more link -> the single service page
			printf(
				'<a href="%1$s" class="tpp-see-more">%2$s</a>',
				esc_url( get_permalink() ),
				esc_html__( 'See More', 'motopress-appointment' )
			);

			do_action( "{$template_name}_before_item_end", $itemArgs );
			?>
		</div>

	</div>
</div>
<?php
do_action( "{$template_name}_after_item", $itemArgs );