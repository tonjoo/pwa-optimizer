<?php 
/**
 * TONJOO_PWA Class.
 *
 * Progressive Web Apps class.
 *
 * @class       TONJOO_PWA
 * @version		1.0
 * @author ebenhaezerbm <eben@tonjoo.com>
 */

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

class TONJOO_PWA { 
	/**
	* Singleton method
	*
	* @return self
	*/
	public static function init() { 
		static $instance = false;

		if ( ! $instance ) { 
			$instance = new TONJOO_PWA();
		}

		return $instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() { 
		$this->options = get_option( 'pwa_optimizer' );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

		if( 'on' == $this->options['offline_mode']['status'] || 'on' == $this->options['assets']['status'] ){ 
			add_action( 'wp_footer', array( $this, 'install_service_worker' ), 20 );
		}
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts($hook) { 
		wp_register_style( 'tonjoo-pwa', tonjoo_pwa()->plugin_url() . '/css/style.css', array(), false );
		wp_register_script( 'tonjoo-pwa', tonjoo_pwa()->plugin_url() . '/js/scripts.js', array( 'jquery' ), '', false );

		$url_sw = home_url('sw.js');
		// check if plugin WPML Multilingual CMS is active
		if ( in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ), true ) ) {
			if ( function_exists('icl_object_id') ) {
				$my_default_lang 	= apply_filters('wpml_default_language', NULL );
        		$site_url 			= apply_filters( 'wpml_permalink', home_url(), $my_default_lang ); 
				$url_sw 			= $site_url .'/sw.js';
			}
		} else if ( in_array( 'polylang/polylang.php', apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ), true ) ) { //check if plugin polylang is active
			// nothing
		}

		$localize_data = apply_filters( 'tonjoo_pwa_localize_data', array( 
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ), 
			'service_worker' 	=> $url_sw 
		) );
		wp_localize_script( 'tonjoo-pwa', 'TONJOO_PWA', $localize_data );

		wp_enqueue_style( 'tonjoo-pwa' );
		wp_enqueue_script( 'tonjoo-pwa' );
	}

	public function install_service_worker() { 
		?>
		<script type="text/javascript">
			// Register Service Worker
			if ('serviceWorker' in navigator) {
				window.addEventListener('load', function() {
					navigator.serviceWorker.register(TONJOO_PWA.service_worker).then(function(registration) {
						// console.log('ServiceWorker registration successful with scope: ', registration.scope);
					}, function(err) {
						console.log('ServiceWorker registration failed: ', err);
					});
				});
			}
		</script>
		<?php 
	}
}

TONJOO_PWA::init();
