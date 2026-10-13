<?php
/**
 * Themify Ultra child theme: MotoPress Appointment service list as an accordion.
 */

/**
 * Enqueue the services accordion script.
 * Loads after jQuery (bundled with WP core and Themify), only where needed.
 */
function tpp_enqueue_service_accordion_script() {

	$script_path = get_stylesheet_directory() . '/assets/js/tpp-service-accordion.js';
	$script_uri  = get_stylesheet_directory_uri() . '/assets/js/tpp-service-accordion.js';

	if ( ! file_exists( $script_path ) ) {
		return;
	}

	wp_enqueue_script(
		'tpp-service-accordion',
		$script_uri,
		array( 'jquery' ),
		filemtime( $script_path ), // cache-bust on every edit
		true // load in the footer
	);
}
add_action( 'wp_enqueue_scripts', 'tpp_enqueue_service_accordion_script' );

/**
 * Shortcode [tpp_services_meta]: price and duration for the current service in the loop.
 */
function tpp_service_meta() {
    ob_start();
    $service_id = get_the_ID();

    // MotoPress stores service fields as _mpa_-prefixed post meta.
    $price    = get_post_meta( $service_id, '_mpa_price', true );
    $duration = get_post_meta( $service_id, '_mpa_duration', true ); // Stored in minutes

    $currency_symbol = '$';
    if ( function_exists( 'mpa_get_currency_symbol' ) ) {
        $currency_symbol = mpa_get_currency_symbol();
    }

    if ( ! empty( $price ) || ! empty( $duration ) ) {
        echo '<div class="custom-service-meta">';
        
        if ( ! empty( $price ) ) {
            echo '<h4 class="service-meta-item price">';
            echo '<strong>' . esc_html__( 'Price:', 'tpp' ) . '</strong> ' . esc_html( $currency_symbol . $price );
            echo '</h4>';
        }
        
        if ( ! empty( $duration ) ) {
            echo '<h4 class="service-meta-item duration">';
            echo '<strong>' . esc_html__( 'Duration:', 'tpp' ) . '</strong> ' . esc_html( $duration ) . ' ' . esc_html__( 'mins', 'tpp' );
            echo '</h4>';
        }
        
        echo '</div>';
    }
    
    return ob_get_clean();
}
add_shortcode('tpp_services_meta', 'tpp_service_meta');

/**
 * Build the service details HTML (meta bar + full content) for a MotoPress service.
 * Data lookup lives here; templates and shortcodes just call it.
 *
 * @param int $service_id The ID of the mpa_service post.
 * @return string HTML output of meta fields and full content.
 */
function get_custom_service_details_html( $service_id = 0 ) {

	$service_id = $service_id ? absint( $service_id ) : get_the_ID();

	if ( ! $service_id || 'mpa_service' !== get_post_type( $service_id ) ) {
		return '';
	}

	$service_post = get_post( $service_id );
	if ( ! $service_post ) {
		return '';
	}

	$price    = get_post_meta( $service_id, '_mpa_price', true );
	$duration = get_post_meta( $service_id, '_mpa_duration', true );

	$currency_symbol = function_exists( 'mpa_get_currency_symbol' )
		? mpa_get_currency_symbol()
		: '$';

	// Filtered so shortcodes and blocks inside the content render.
	$full_content = apply_filters( 'the_content', $service_post->post_content );

	$meta_html = '';

	if ( '' !== $price ) {
		$meta_html .= sprintf(
			'<h4 class="meta-price"><strong>%s</strong> %s</h4>',
			esc_html__( 'Price:', 'tpp' ),
			esc_html( $currency_symbol . $price )
		);
	}

	if ( '' !== $duration ) {
		$meta_html .= sprintf(
			'<h4 class="meta-duration"><strong>%s</strong> %s %s</h4>',
			esc_html__( 'Duration:', 'tpp' ),
			esc_html( $duration ),
			esc_html__( 'mins', 'tpp' )
		);
	}

	$output = '<div class="custom-service-container">';

	if ( $meta_html ) {
		$output .= '<div class="custom-service-meta">' . $meta_html . '</div>';
	}

	if ( $full_content ) {
		$output .= '<div class="custom-service-description">' . $full_content . '</div>';
	}

	$output .= '</div>';

	/**
	 * Filter the final service details HTML.
	 *
	 * @param string $output     The generated HTML.
	 * @param int    $service_id The service post ID.
	 */
	return apply_filters( 'custom_service_details_html', $output, $service_id );
}

/**
 * Shortcode wrapper: [custom_service_details id="123"]
 */
add_shortcode( 'custom_service_details', function ( $atts ) {
	$atts = shortcode_atts(
		array(
			'id' => 0,
		),
		$atts,
		'custom_service_details'
	);

	return get_custom_service_details_html( absint( $atts['id'] ) );
} );

/**
 * Services show their full description in the accordion panel, not a trimmed excerpt.
 */
add_filter( 'get_the_excerpt', 'tpp_mpa_full_content_instead_of_excerpt', 20, 2 );
function tpp_mpa_full_content_instead_of_excerpt( $excerpt, $post = null ) {
	$post = $post ?: get_post();

	if ( $post && 'mpa_service' === get_post_type( $post ) ) {
		return apply_filters( 'the_content', $post->post_content );
	}

	return $excerpt;
}
