<?php 
/** 
 * Plugin Name: PWA Optimizer
 * Plugin URI: https://www.tonjoostudio.com
 * Description: PWA Optimizer plugin to optimize loading performance with minimum configuration using Progressive Web Apps approach
 * Author: Tonjoo
 * Author URI: https://tonjoo.com
 * Version: 1.0.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Network: false
 */

defined( 'ABSPATH' ) OR exit;

defined( 'WPINC' ) OR exit;

if ( ! class_exists( 'TONJOO_PWA_FACTORY' ) ) :

/**
 * Main TONJOO_PWA_FACTORY Class
 *
 * @class TONJOO_PWA_FACTORY
 * @version	1.0
 */
final class TONJOO_PWA_FACTORY {

	/**
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * @var TONJOO_PWA_FACTORY The single instance of the class
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * Main TONJOO_PWA_FACTORY Instance
	 *
	 * Ensures only one instance of TONJOO_PWA_FACTORY is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return TONJOO_PWA_FACTORY - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * TONJOO_PWA_FACTORY Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'tonjoo_pwa_loaded' );
	}

	/**
	 * Hook into actions and filters
	 * @since  2.3
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );

		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * All install stuff
	 * @return [type] [description]
	 */
	public function install() {
		$this->set_default_options();

		$this->render_service_worker();
		$this->render_offline_page();
	}

	/**
	 * All uninstall stuff
	 * @return [type] [description]
	 */
	public function uninstall() {

	}

	/**
	 * Define HM Constants
	 */
	private function define_constants() {

		$this->define( 'TONJOO_PWA_PLUGIN_FILE', __FILE__ );
		$this->define( 'TONJOO_PWA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'TONJOO_PWA_VERSION', $this->version );
		$this->define( 'TONJOO_PWA_SLUG', 'pwa_optimizer' );
	}

	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		// all public includes
		include_once( 'inc/manifest.class.php' );
		include_once( 'inc/offline-mode.class.php' );
		include_once( 'inc/assets.class.php' );

		if ( $this->is_request( 'admin' ) ) {
			include_once( 'inc/admin/settings.class.php' );
		}

		if ( $this->is_request( 'ajax' ) ) {
			// include_once( 'inc/ajax/..*.php' );
		}

		if ( $this->is_request( 'frontend' ) ) {
			include_once( 'inc/pwa.class.php' );
			include_once( 'inc/service-worker.class.php' );
			include_once( 'inc/lazyload.class.php' );
		}
	}

	/**
	 * Init TONJOO_PWA_FACTORY when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		do_action( 'before_tonjoo_pwa_init' );

		// Init action
		do_action( 'after_tonjoo_pwa_init' );
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template path.
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'tonjoo_pwa_template_path', 'templates/' );
	}

	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	function set_default_options() {
		$options = [];

		if( false !== get_option( 'pwa_optimizer' ) ) return;

		if( ! isset($options['offline_mode']) ) {
			$options['offline_mode']['status'] = 'on';
			$options['offline_mode']['offline_page'] = file_get_contents( $this->plugin_url() . '/src/offline-page.html' );
		}

		if( ! isset($options['assets']) ) {
			$options['assets']['status'] = 'on';
			$options['assets']['pgcache_reject_uri'] = '/wp-admin(.*)|(.*)preview=true(.*)/';
		}

		if( ! isset($options['manifest']) ) {
			$options['manifest']['status'] = 'off';
			$options['manifest']['app_name'] = get_bloginfo('name');
			$options['manifest']['short_name'] = get_bloginfo('name');
			$options['manifest']['icons'] = [];
			$options['manifest']['app_description'] = get_bloginfo('description');
			$options['manifest']['start_url'] = get_bloginfo('url');
			$options['manifest']['orientation'] = 'portrait';
			$options['manifest']['theme_color'] = '#ffffff';
			$options['manifest']['background_color'] = '#ffffff';
			$options['manifest']['related_apps'][] = array( 'platform' => 'play', 'id' => '' );
		}

		if( ! isset($options['lazyload']) ) {
			$options['lazyload']['status'] = 'off';
			$options['lazyload']['preload_image'] = 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';
			$options['lazyload']['css_class'] = 'pwa-image-responsive';
			$options['lazyload']['root_margin'] = 0;
			$options['lazyload']['threshold'] = 0;
		}

		add_option( 'pwa_optimizer', $options, '', 'no' );
		add_option( 'pwa_optimizer_default_settings', $options, '', 'no' );
	}

	function render_service_worker() { 
		$options = get_option( 'pwa_optimizer' );

		$filename = get_home_path() . 'sw.js';

		if( file_exists($filename) ){ 
			unlink($filename);
		}

		$pgcache_reject 	= '';
		$precache_assets 	= '';

		if( 'on' == $options['assets']['status'] ){
			if( ! empty( $options['assets']['pgcache_reject_uri'] ) ){
				$pgcache_reject_uri = explode( "\n", $options['assets']['pgcache_reject_uri'] );
				if( $pgcache_reject_uri ) {
					foreach ($pgcache_reject_uri as $key => $value) {
						$pgcache_reject .= <<< EOT
\n
	workbox.routing.registerRoute($value, workbox.strategies.networkOnly());
EOT;
					}
				}
			} 

			$precache_assets = <<< EOT
// Stale while revalidate for JS and CSS that are not precache
	workbox.routing.registerRoute(
		/\.(?:js|css)$/,
		workbox.strategies.staleWhileRevalidate({
			cacheName: 'js-css-cache'
		}),
	);

	// We want no more than 50 images in the cache. We check using a cache first strategy
	workbox.routing.registerRoute(/\.(?:png|gif|jpg)$/,
		workbox.strategies.cacheFirst({
		cacheName: 'images-cache',
			cacheExpiration: {
				maxEntries: 50
			}
		})
	);

	// We need cache fonts if any
	workbox.routing.registerRoute(/(.*)\.(?:woff|eot|woff2|ttf|svg)$/,
		workbox.strategies.cacheFirst({
		cacheName: 'external-font-cache',
			cacheExpiration: {
				maxEntries: 20
			},
			cacheableResponse: {
				statuses: [0, 200]
			}
		})
	);

	workbox.routing.registerRoute(/https:\/\/fonts.googleapis.com\/(.*)/,
		workbox.strategies.cacheFirst({
			cacheName: 'google-font-cache',
			cacheExpiration: {
				maxEntries: 20
			},
			cacheableResponse: {statuses: [0, 200]}
		})
	);
EOT;
		}

		$revision = md5( $options['offline_mode']['offline_page'] );

		$precache 		= '';
		$offline_script = '';

		if( 'on' == $options['offline_mode']['status'] ){
			$precache = <<< EOT
workbox.precaching.precacheAndRoute([
		{ 
			'url': 'offline-page.html', 
			'revision': '{$revision}' 
		}
	]);
EOT;

			$offline_script = <<< EOT
// diconvert ke es5
	const matcher = ({event}) => event.request.mode === 'navigate';
	const handler = (obj) => fetch(obj.event.request).catch(() => caches.match('/offline-page.html'));

	workbox.routing.registerRoute(matcher, handler);
EOT;
		} 

		$script = <<< EOT
importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.0.0/workbox-sw.js');

if (workbox) {
	// make new service worker code available instantly
	workbox.skipWaiting();
	workbox.clientsClaim();

	{$precache}

	{$pgcache_reject}

	{$precache_assets}

	{$offline_script}
} else {
	console.log(`Boo! Workbox didn't load 😬`);
}
EOT;

		$a = fopen( $filename, 'w' ) or die( 'Unable to open file!. Please check your permission.' );
		fwrite( $a, $script );
		fclose( $a );
		chmod( $filename, 0755 );
	}

	function render_offline_page() { 
		$options = get_option( 'pwa_optimizer' );

		if( 'on' == $options['offline_mode']['status'] ){
			$filename = get_home_path() . 'offline-page.html';

			$a = fopen( $filename, 'w' ) or die( 'Unable to open file!. Please check your permission.' );
			fwrite( $a, $options['offline_mode']['offline_page'] );
			fclose( $a );
			chmod( $filename, 0755 );
		}
	}
}

endif;

/**
 * Returns the main instance of HM to prevent the need to use globals.
 *
 * @since  1.0
 * @return TONJOO_PWA_FACTORY
 */
function tonjoo_pwa() {
	return TONJOO_PWA_FACTORY::instance();
}

// Global for backwards compatibility.
tonjoo_pwa();
