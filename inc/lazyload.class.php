<?php 
/**
 * TONJOO_PWA_LAZYLOAD Class.
 *
 * LazyLoad class.
 *
 * @class       TONJOO_PWA_LAZYLOAD
 * @version		1.0
 * @author ebenhaezerbm <eben@tonjoo.com>
 */

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

class TONJOO_PWA_LAZYLOAD { 
	/**
	* Singleton method
	*
	* @return self
	*/
	public static function init() { 
		static $instance = false;

		if ( ! $instance ) { 
			$instance = new TONJOO_PWA_LAZYLOAD();
		}

		return $instance;
	}

	protected $options;

	/**
	 * Constructor
	 */
	public function __construct() { 
		$this->options = get_option( 'pwa_optimizer' );

		if( 'on' == $this->options['lazyload']['status'] ){
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// plugin hook
			add_filter( 'tonjoo_pwa_localize_data', array( $this, 'localize_data' ) );

			add_action( "init", array( $this, "register_shortcodes" ) );

			// replace src to data-src
			add_filter( 'the_content', array( $this, 'replace_to_data_src' ) );
			add_filter( 'post_thumbnail_html', array( $this, 'replace_to_data_src' ) );
			add_filter( 'get_avatar', array( $this, 'replace_to_data_src' ), 11 );

			add_filter( 'tonjoo_pwa_do_replace_observer', array( $this, 'lazyloadImages' ) );
			add_filter( 'tonjoo_pwa_do_replace_observer', array( $this, 'lazyloadIframe' ) );
		}
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts() { 
		wp_register_script( 'tonjoo-pwa-lazyload', tonjoo_pwa()->plugin_url() . '/js/lazyload.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'tonjoo-pwa-lazyload' );
	}

	public function localize_data($data) { 
		$rootMargin = intval($this->options['lazyload']['root_margin']);
		$threshold = intval($this->options['lazyload']['threshold']);

		$data['intersection_observer'] = array( 
			'root_margin' => $rootMargin, 
			'threshold' => $threshold 
		);

		return $data;
	}

	public function register_shortcodes() {
		add_shortcode( 'pwa-optimizer-lazyload', array( $this, "shortcode_callback" ), 10, 2 );
	}

	public function shortcode_callback($atts, $content=null) {
		$args =  shortcode_atts( array(
			'type' 		=> 'image', 
			'src' 		=> $this->options['lazyload']['preload_image'], 
			'alt' 		=> '', 
			'id' 		=> '', 
			'class' 	=> $this->options['lazyload']['css_class'], 
			'style' 	=> '', 
			'width' 	=> '', 
			'height' 	=> '' 
		), $atts );

		$type 	= str_replace("&quot;", "", $args['type'] );
		$src 	= str_replace("&quot;", "", $args['src'] );

		if( empty($src) ) return $content;

		$attr['src'] 		= 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';
		$attr['data-src'] 	= $src;

		if( !empty( str_replace("&quot;", "", $args['alt'] ) ) ) { 
			$attr['style'] = str_replace("&quot;", "", $args['style'] );
		}

		if( !empty( str_replace("&quot;", "", $args['id'] ) ) ) { 
			$attr['id'] = str_replace("&quot;", "", $args['id'] );
		}

		if( !empty( str_replace("&quot;", "", $args['class'] ) ) ) { 
			$attr['class'] = 'lazy-hidden ' . str_replace("&quot;", "", $args['class'] );
		} else { 
			$attr['class'] = 'lazy-hidden';
		}

		if( !empty( str_replace("&quot;", "", $args['alt'] ) ) ) { 
			if( 'image' == $type ) { 
				$attr['alt'] = str_replace("&quot;", "", $args['alt'] );
			}
		}

		if( !empty( str_replace("&quot;", "", $args['width'] ) ) ) { 
			$attr['width'] = str_replace("&quot;", "", $args['width'] );
		}

		if( !empty( str_replace("&quot;", "", $args['height'] ) ) ) { 
			$attr['height'] = str_replace("&quot;", "", $args['height'] );
		}

		ob_start();

		if( 'iframe' == $type ) { 
			echo '<iframe '.implode( ' ', array_map( 
				function ($value, $key) { return sprintf("%s='%s'", $key, $value); },
				$attr,
				array_keys($attr) ) ).'></iframe>'; 
		} else { 
			echo '<img '.implode( ' ', array_map( 
				function ($value, $key) { return sprintf("%s='%s'", $key, $value); },
				$attr,
				array_keys($attr) ) ).'>'; 
		}

		$content = ob_get_contents();

		ob_end_clean();

		return $content;
	}

	public static function lazyloadIframe($html) {
		if ( is_admin() ) 
			return $html;

		if ( is_feed() ) 
			return $html;

		// do not save if this is an ajax routine
		if ( defined('DOING_AJAX') && DOING_AJAX ) 
			return $html;

		$pwa_optimizer = get_option( 'pwa_optimizer' );
		$options = isset( $pwa_optimizer['lazyload'] ) ? $pwa_optimizer['lazyload'] : array();

		$matches = array();

		preg_match_all( '/<iframe[\s\r\n]+.*?>/is', $html, $matches );

		$search = array();
		$replace = array();

		$css_class = isset($options['css_class']) ? $options['css_class'] : '';

		if( isset($options['lazyload']['preload_image']) && ! empty($options['preload_image']) ) {
			$placeholder_url_used = $options['lazyload']['preload_image'];
		} else {
			$placeholder_url_used = 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';
		}

		foreach ( $matches[0] as $iframeHTML ) {

			// replace the src and add the data-src attribute
			$replaceHTML = preg_replace( '/<iframe(.*?)src=/is', '<iframe$1src="' . esc_attr( $placeholder_url_used ) . '" data-lazy-type="text/html" data-src=', $iframeHTML );

			// add the lazy class to the iframe element
			if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
				$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1lazy lazy-hidden '.$css_class.' $2$1', $replaceHTML );
			} else {
				$replaceHTML = preg_replace( '/<iframe/is', '<iframe class="lazy lazy-hidden '.$css_class.'"', $replaceHTML );
			}

			$replaceHTML .= '<noscript>' . $iframeHTML . '</noscript>';

			array_push( $search, $iframeHTML );
			array_push( $replace, $replaceHTML );
		}

		$html = str_replace( $search, $replace, $html );

		return $html;
	}

	public static function lazyloadImages($html) {
		if ( is_admin() ) 
			return $html;

		if ( is_feed() ) 
			return $html;

		// do not save if this is an ajax routine
		if ( defined('DOING_AJAX') && DOING_AJAX ) 
			return $html;

		$pwa_optimizer = get_option( 'pwa_optimizer' );
		$options = isset( $pwa_optimizer['lazyload'] ) ? $pwa_optimizer['lazyload'] : array();

		$matches = array();

		preg_match_all( '/<img[\s\r\n]+.*?>/is', $html, $matches );

		$search = array();
		$replace = array();

		$css_class = isset($options['css_class']) ? $options['css_class'] : '';

		if( isset($options['preload_image']) && ! empty($options['preload_image']) ){
			$placeholder_url_used = $options['preload_image'];
		} else {
			$placeholder_url_used = 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';
		}

		foreach ( $matches[0] as $imgHTML ) {

			// don't do the replacement if the image is a data-uri
			if ( ! preg_match( "/src=['\"]data:image/is", $imgHTML ) ) {
				if( preg_match( '/class=["\'].*?wp-image-([0-9]*)/is', $imgHTML, $id_matches ) ) {
					$img_id = intval($id_matches[1]);
				}

				// replace the src and add the data-src attribute
				$replaceHTML = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . esc_attr( $placeholder_url_used ) . '" data-lazy-type="image" data-src=', $imgHTML );

				// also replace the srcset (responsive images)
				$replaceHTML = str_replace( 'srcset', 'data-lazy-srcset', $replaceHTML );
				// replace sizes to avoid w3c errors for missing srcset
				$replaceHTML = str_replace( 'sizes', 'data-lazy-sizes', $replaceHTML );

				// add the lazy class to the img element
				if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
					$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1lazy lazy-hidden '.$css_class.' $2$1', $replaceHTML );
				} else {
					$replaceHTML = preg_replace( '/<img/is', '<img class="lazy lazy-hidden '.$css_class.'"', $replaceHTML );
				}

				$replaceHTML .= '<noscript>' . $imgHTML . '</noscript>';

				array_push( $search, $imgHTML );
				array_push( $replace, $replaceHTML );
			}
		}

		$html = str_replace( $search, $replace, $html );

		return $html;
	}

	public function replace_to_data_src($html) {
		return apply_filters( 'tonjoo_pwa_do_replace_observer', $html );
	}
}

TONJOO_PWA_LAZYLOAD::init();
