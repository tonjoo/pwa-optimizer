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
		$this->options = get_option( 'pwa_optimizer' );

		// add_action( 'add_option_pwa_optimizer', array( $this, 'added_option' ), 20, 2 );
		add_action( 'update_option_pwa_optimizer', array( $this, 'updated_option' ), 20, 3 );

		if( 'on' == $this->options['manifest']['status'] ){ 
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'wp_head', array( $this, 'addLinkToHead' ), 10 );
			add_action( 'wp_footer', array( $this, 'install_prompt' ), 20 );
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

	public function added_option( $option, $value ){
		$this->render_manifest( $value['manifest'] );
	}

	public function updated_option( $old_value, $new_value, $option ){
		$this->render_manifest( $new_value['manifest'] );
	}

	public function render_manifest($value){
		$app_name = isset($value['app_name']) ? $value['app_name'] : '';
		$short_name = isset($value['short_name']) ? $value['short_name'] : '';
		$app_description = isset($value['app_description']) ? $value['app_description'] : '';
		$start_url = isset($value['start_url']) ? $value['start_url'] : '';
		$theme_color = isset($value['theme_color']) ? $value['theme_color'] : '';
		$bg_color = isset($value['background_color']) ? $value['background_color'] : '';

		$icons = [];
		if( isset($value['icons']) && is_array($value['icons']) && ! empty($value['icons']) ){ 
			foreach ($value['icons'] as $k => $v) {
				$size = str_replace( 'logo_', '', $k );

				$icons[] = array( 
					'src' 	=> $v, 
					'type' 	=> 'image/png', 
					'sizes' => sprintf( '%dx%d', $size, $size ) 
				);
			}
		}

		$related_apps = [];
		if( isset($value['related_apps']) && is_array($value['related_apps']) && ! empty($value['related_apps']) ){ 
			foreach ($value['related_apps'] as $app) {
				if( ! empty($app['platform']) || ! empty($app['id']) ){
					$related_apps[] = array( 
						'platform' => $app['platform'], 
						'id' => $app['id'] 
					);
				}
			}
		}

		$response = array( 
			"name" 					=> $app_name, 
			"short_name" 			=> $short_name, 
			"icons" 				=> $icons, 
			"description" 			=> $app_description, 
			"start_url" 			=> $start_url, 
			"display" 				=> 'standalone', 
			"orientation" 			=> 'portrait', 
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
