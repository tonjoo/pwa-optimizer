<?php 
/**
 * TONJOO_PWA_SERVICE_WORKER Class.
 *
 * Service Worker class.
 *
 * @class       TONJOO_PWA_SERVICE_WORKER
 * @version		1.0
 * @author ebenhaezerbm <eben@tonjoo.com>
 */

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

class TONJOO_PWA_SERVICE_WORKER { 
	/**
	* Singleton method
	*
	* @return self
	*/
	public static function init() { 
		static $instance = false;

		if ( ! $instance ) { 
			$instance = new TONJOO_PWA_SERVICE_WORKER();
		}

		return $instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {}
}

TONJOO_PWA_SERVICE_WORKER::init();
