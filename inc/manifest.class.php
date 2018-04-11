<?php 
/**
 * TONJOO_PWA_MANIFEST Class.
 *
 * Manifest class.
 *
 * @class       TONJOO_PWA_MANIFEST
 * @version		1.0
 * @author ebenhaezerbm <eben@tonjoo.com>
 */

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

class TONJOO_PWA_MANIFEST { 
	/**
	* Singleton method
	*
	* @return self
	*/
	public static function init() { 
		static $instance = false;

		if ( ! $instance ) { 
			$instance = new TONJOO_PWA_MANIFEST();
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

		if( isset( $this->options['manifest']['status'] ) && 'on' == $this->options['manifest']['status'] ){ 
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'wp_head', array( $this, 'addLinkToHead' ), 10 );
			add_action( 'wp_footer', array( $this, 'install_prompt' ), 20 );

			add_action( 'add_option_tonjoo_pwa_manifest', array( $this, 'updated_option' ), 10, 3 );
			add_action( 'update_option_tonjoo_pwa_manifest', array( $this, 'updated_option' ), 10, 3 );
		}
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts() {}

	public function addLinkToHead(){
		echo '<link rel="manifest" href="'.home_url('manifest.json').'">';
	}

	public function install_prompt() { 
		?>
		<script type="text/javascript">
			// Add Homepage Prompt
			window.addEventListener('beforeinstallprompt', function(e) {
				e.userChoice.then(function(choiceResult) {

					console.log(choiceResult.outcome);

					if( choiceResult.outcome == 'dismissed' ) {
						console.log('User cancelled home screen install');
					} 
					else {
						console.log('User added to home screen');
					}
				});
			});
		</script>
		<?php 
	}

	public function updated_option( $old_value, $value, $option ){
		global $wp_query;

		$app_name 			= get_bloginfo('name');
		$short_name 		= get_bloginfo('name');
		$icons 				= [];
		$app_description 	= get_bloginfo('description');
		$start_url 			= get_bloginfo('url');
		$orientation 		= 'portrait';
		$theme_color 		= '#ffffff';
		$bg_color 			= '#ffffff';
		$related_apps 		= [];

		if( isset( $this->options['manifest']['status'] ) && 'on' == $this->options['manifest']['status'] ){ 
			if( isset( $this->options['manifest']['app_name'] ) && !empty( $this->options['manifest']['app_name'] ) ){ 
				$app_name = $this->options['manifest']['app_name'];
			}

			if( isset( $this->options['manifest']['short_name'] ) && !empty( $this->options['manifest']['short_name'] ) ){ 
				$short_name = $this->options['manifest']['short_name'];
			}

			if( isset( $this->options['manifest']['logo_48'] ) && !empty( $this->options['manifest']['logo_48'] ) ){ 
				$icons[] = array( 
					'src' => $this->options['manifest']['logo_48'], 
					'type' => 'image/png', 
					'sizes' => '48x48' 
				);
			}

			if( isset( $this->options['manifest']['logo_48'] ) && !empty( $this->options['manifest']['logo_48'] ) ){ 
				$icons[] = array( 
					'src' => $this->options['manifest']['logo_48'], 
					'type' => 'image/png', 
					'sizes' => '96x96' 
				);
			}

			if( isset( $this->options['manifest']['logo_128'] ) && !empty( $this->options['manifest']['logo_128'] ) ){ 
				$icons[] = array( 
					'src' => $this->options['manifest']['logo_128'], 
					'type' => 'image/png', 
					'sizes' => '128x128' 
				);
			}

			if( isset( $this->options['manifest']['logo_144'] ) && !empty( $this->options['manifest']['logo_144'] ) ){ 
				$icons[] = array( 
					'src' => $this->options['manifest']['logo_144'], 
					'type' => 'image/png', 
					'sizes' => '144x144' 
				);
			}

			if( isset( $this->options['manifest']['logo_152'] ) && !empty( $this->options['manifest']['logo_152'] ) ){ 
				$icons[] = array( 
					'src' => $this->options['manifest']['logo_152'], 
					'type' => 'image/png', 
					'sizes' => '152x152' 
				);
			}

			if( isset( $this->options['manifest']['logo_192'] ) && !empty( $this->options['manifest']['logo_192'] ) ){ 
				$icons[] = array( 
					'src' => $this->options['manifest']['logo_192'], 
					'type' => 'image/png', 
					'sizes' => '192x192' 
				);
			}

			if( isset( $this->options['manifest']['logo_256'] ) && !empty( $this->options['manifest']['logo_256'] ) ){ 
				$icons[] = array( 
					'src' => $this->options['manifest']['logo_256'], 
					'type' => 'image/png', 
					'sizes' => '256x256' 
				);
			}

			if( isset( $this->options['manifest']['logo_384'] ) && !empty( $this->options['manifest']['logo_384'] ) ){ 
				$icons[] = array( 
					'src' => $this->options['manifest']['logo_384'], 
					'type' => 'image/png', 
					'sizes' => '384x384' 
				);
			}

			if( isset( $this->options['manifest']['logo_512'] ) && !empty( $this->options['manifest']['logo_512'] ) ){ 
				$icons[] = array( 
					'src' => $this->options['manifest']['logo_512'], 
					'type' => 'image/png', 
					'sizes' => '512x512' 
				);
			}

			if( isset( $this->options['manifest']['app_description'] ) && !empty( $this->options['manifest']['app_description'] ) ){ 
				$app_description = $this->options['manifest']['app_description'];
			}

			if( isset( $this->options['manifest']['start_url'] ) && !empty( $this->options['manifest']['start_url'] ) ){ 
				$start_url = $this->options['manifest']['start_url'];
			}

			if( isset( $this->options['manifest']['theme_color'] ) && !empty( $this->options['manifest']['theme_color'] ) ){ 
				$theme_color = $this->options['manifest']['theme_color'];
			}

			if( isset( $this->options['manifest']['background_color'] ) && !empty( $this->options['manifest']['background_color'] ) ){ 
				$bg_color = $this->options['manifest']['background_color'];
			}

			if( isset( $this->options['manifest']['mobile_apps'] ) && !empty( $this->options['manifest']['mobile_apps'] ) ){ 
				$related_apps[] = array( 
					'platform' => 'play', 
					'id' => $this->options['manifest']['mobile_apps'] 
				);
			}
		}

		$response = array( 
			"name" 					=> $app_name, 
			"short_name" 			=> $short_name, 
			"icons" 				=> $icons, 
			"description" 			=> $app_description, 
			"start_url" 			=> $start_url, 
			"display" 				=> "standalone", 
			"orientation" 			=> $orientation, 
			"theme_color" 			=> $theme_color, 
			"background_color" 		=> $bg_color, 
			"related_applications" 	=> $related_apps 
		);

		$filename = get_home_path().'manifest.json';

		if( file_exists($filename) ){ 
			unlink($filename);
		}

		$fp = fopen( $filename, 'w' );
		fwrite( $fp, json_encode( $response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
		fclose( $fp );
		chmod( $filename, 0755 );
	}
}

TONJOO_PWA_MANIFEST::init();
