<?php 
/**
 * TONJOO_PWA_OFFLINE_MODE Class.
 *
 * Service Worker class.
 *
 * @class       TONJOO_PWA_OFFLINE_MODE
 * @version		1.0
 * @author ebenhaezerbm <eben@tonjoo.com>
 */

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

class TONJOO_PWA_OFFLINE_MODE { 
	/**
	* Singleton method
	*
	* @return self
	*/
	public static function init() { 
		static $instance = false;

		if ( ! $instance ) { 
			$instance = new TONJOO_PWA_OFFLINE_MODE();
		}

		return $instance;
	}

	protected $options;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->options = array( 
			'offline_mode' 	=> get_option( 'tonjoo_pwa_offline_mode' ), 
			'assets' 		=> get_option( 'tonjoo_pwa_assets' ), 
			'manifest' 		=> get_option( 'tonjoo_pwa_manifest' ), 
			'lazyload' 		=> get_option( 'tonjoo_pwa_lazy_load' ) 
		);

		add_action( 'add_option_tonjoo_pwa_offline_mode', array( $this, 'added_option' ), 10, 2 );
		add_action( 'update_option_tonjoo_pwa_offline_mode', array( $this, 'updated_option' ), 10, 3 );
	}

	public function added_option( $option, $value ) { 
		if( ! isset($value['offline_page']) ) 
			return;

		$this->render_service_worker( $value );
		$this->render_offline_page( $value['offline_page'] );
	}

	public function updated_option( $old_value, $new_value, $option ) { 
		if( ! isset($new_value['offline_page']) ) 
			return;

		$this->render_service_worker( $new_value );
		$this->render_offline_page( $new_value['offline_page'] );
	}

	public function render_service_worker($new_value) { 
		$filename = get_home_path() . 'sw.js';

		if( file_exists($filename) ){ 
			unlink($filename);
		}

		$pgcache_reject 	= '';
		$precache_assets 	= '';

		if( isset($this->options['assets']['status']) && 'on' == $this->options['assets']['status'] ){
			$pgcache_reject = <<< EOT
workbox.routing.registerRoute(/wp-admin(.*)|(.*)preview=true(.*)/,
		workbox.strategies.networkOnly()
	);
EOT;
			if( isset($this->options['assets']['pgcache_reject_uri']) && ! empty( $this->options['assets']['pgcache_reject_uri'] ) ){
				$pgcache_reject_uri = explode( "\n", $this->options['assets']['pgcache_reject_uri'] );
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

		$precache 		= '';
		$offline_script = '';

		$revision = isset($new_value['offline_page']) ? md5($new_value['offline_page']): 'eee43012';
		if( isset($new_value['status']) && 'on' == $new_value['status'] ){
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

	public function render_offline_page($html) { 
		$filename = get_home_path() . 'offline-page.html';

		if( file_exists($filename) ){ 
			unlink($filename);
		}

		$a = fopen( $filename, 'w' ) or die( 'Unable to open file!. Please check your permission.' );
		fwrite( $a, $html );
		fclose( $a );
		chmod( $filename, 0755 );
	}
}

TONJOO_PWA_OFFLINE_MODE::init();
