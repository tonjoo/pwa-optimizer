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

			add_action( 'add_option_tonjoo_pwa_manifest', array( $this, 'added_option' ), 10, 2 );
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

	public function added_option( $option, $value ){}

	public function updated_option( $old_value, $new_value, $option ){
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

		if( isset( $new_value['status'] ) && 'on' == $new_value['status'] ){ 
			if( isset( $new_value['app_name'] ) && !empty( $new_value['app_name'] ) ){ 
				$app_name = $new_value['app_name'];
			}

			if( isset( $new_value['short_name'] ) && !empty( $new_value['short_name'] ) ){ 
				$short_name = $new_value['short_name'];
			}

			if( isset( $new_value['logo_48'] ) && !empty( $new_value['logo_48'] ) ){ 
				$icons[] = array( 
					'src' => $new_value['logo_48'], 
					'type' => 'image/png', 
					'sizes' => '48x48' 
				);
			}

			if( isset( $new_value['logo_48'] ) && !empty( $new_value['logo_48'] ) ){ 
				$icons[] = array( 
					'src' => $new_value['logo_48'], 
					'type' => 'image/png', 
					'sizes' => '96x96' 
				);
			}

			if( isset( $new_value['logo_128'] ) && !empty( $new_value['logo_128'] ) ){ 
				$icons[] = array( 
					'src' => $new_value['logo_128'], 
					'type' => 'image/png', 
					'sizes' => '128x128' 
				);
			}

			if( isset( $new_value['logo_144'] ) && !empty( $new_value['logo_144'] ) ){ 
				$icons[] = array( 
					'src' => $new_value['logo_144'], 
					'type' => 'image/png', 
					'sizes' => '144x144' 
				);
			}

			if( isset( $new_value['logo_152'] ) && !empty( $new_value['logo_152'] ) ){ 
				$icons[] = array( 
					'src' => $new_value['logo_152'], 
					'type' => 'image/png', 
					'sizes' => '152x152' 
				);
			}

			if( isset( $new_value['logo_192'] ) && !empty( $new_value['logo_192'] ) ){ 
				$icons[] = array( 
					'src' => $new_value['logo_192'], 
					'type' => 'image/png', 
					'sizes' => '192x192' 
				);
			}

			if( isset( $new_value['logo_256'] ) && !empty( $new_value['logo_256'] ) ){ 
				$icons[] = array( 
					'src' => $new_value['logo_256'], 
					'type' => 'image/png', 
					'sizes' => '256x256' 
				);
			}

			if( isset( $new_value['logo_384'] ) && !empty( $new_value['logo_384'] ) ){ 
				$icons[] = array( 
					'src' => $new_value['logo_384'], 
					'type' => 'image/png', 
					'sizes' => '384x384' 
				);
			}

			if( isset( $new_value['logo_512'] ) && !empty( $new_value['logo_512'] ) ){ 
				$icons[] = array( 
					'src' => $new_value['logo_512'], 
					'type' => 'image/png', 
					'sizes' => '512x512' 
				);
			}

			if( isset( $new_value['app_description'] ) && !empty( $new_value['app_description'] ) ){ 
				$app_description = $new_value['app_description'];
			}

			if( isset( $new_value['start_url'] ) && !empty( $new_value['start_url'] ) ){ 
				$start_url = $new_value['start_url'];
			}

			if( isset( $new_value['theme_color'] ) && !empty( $new_value['theme_color'] ) ){ 
				$theme_color = $new_value['theme_color'];
			}

			if( isset( $new_value['background_color'] ) && !empty( $new_value['background_color'] ) ){ 
				$bg_color = $new_value['background_color'];
			}

			if( isset( $new_value['mobile_apps'] ) && !empty( $new_value['mobile_apps'] ) ){ 
				$related_apps[] = array( 
					'platform' => 'play', 
					'id' => $new_value['mobile_apps'] 
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
