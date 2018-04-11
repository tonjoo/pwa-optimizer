<?php
/**
 * TONJOO_PWA_ADMIN Class.
 *
 * @class       TONJOO_PWA_ADMIN
 * @version		1.0
 * @author ebenhaezerbm <eben@tonjoo.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * TONJOO_PWA_ADMIN class.
 */
class TONJOO_PWA_ADMIN {

	private $settings_api;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->includes();

		$this->settings_api = new TONJOO_PWA_SETTINGS;

        $plugin_basename = TONJOO_PWA_PLUGIN_BASENAME;
        add_filter( "plugin_action_links_$plugin_basename", array($this, "plugin_setting_link") );

		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_menu', array($this, 'add_menu') );

        add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );

        add_action( 'wp_ajax_tonjoo_pwa_change_file_or_dir_permission', array($this, 'change_file_or_dir_permission') );
	}

	/**
     * Add setting button on plugin actions
     */
    function plugin_setting_link($links) { 
        $settings_link = '<a href="options-general.php?page='.TONJOO_PWA_SLUG.'">' . __( 'Settings', 'tonjoo' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public function add_menu(){
        add_options_page( __( 'PWA Optimizer', 'tonjoo' ), __( 'PWA Optimizer', 'tonjoo' ), 'manage_options', TONJOO_PWA_SLUG, array($this, 'admin_opt') );
	}

	public function admin_opt(){
	 	echo '<div class="wrap">';
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();
        echo '</div>';
	}

	function admin_init() {
        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );
        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_enqueue_scripts($hook) {
        if ( 'settings_page_'.TONJOO_PWA_SLUG == $hook ) { 
            // ace editor
            wp_register_script( 'ace-monokai', tonjoo_pwa()->plugin_url() . '/js/ace-min-noconflict-css-monokai/ace.js', array(), false, true );

            // bootstrap switch
            // wp_register_style( 'bootstrap-switch', tonjoo_pwa()->plugin_url() . '/css/bootstrap-switch.min.css', array(), false );
            // wp_register_script( 'bootstrap-switch', tonjoo_pwa()->plugin_url() . '/js/bootstrap-switch.min.js', array(), false, true );

            wp_register_style( 'tonjoo-pwa', tonjoo_pwa()->plugin_url() . '/css/admin.css', array(), false );
            wp_register_script( 'tonjoo-pwa', tonjoo_pwa()->plugin_url() . '/js/admin.js', array( 'jquery' ), '', true );

            $localize_data = array( 
                'ajaxurl'   => admin_url( 'admin-ajax.php' ), 
                'nonce'     => wp_create_nonce( 'tonjoo-pwa' ) 
            );
            wp_localize_script( 'tonjoo-pwa', 'TONJOO_PWA', $localize_data );

            wp_enqueue_script( 'ace-monokai' );
            // wp_enqueue_style( 'bootstrap-switch' );
            // wp_enqueue_script( 'bootstrap-switch' );

            wp_enqueue_style( 'tonjoo-pwa' );
            wp_enqueue_script( 'tonjoo-pwa' );
        }
    }

    function change_file_or_dir_permission() {
        $response = array( 
            'success' => false, 
            'message' => __( 'Something went wrong!', 'tonjoo' ) 
        );

        if( ! isset($_POST['nonce']) || ! wp_verify_nonce( $_POST['nonce'], 'tonjoo-pwa' ) ){ 
            wp_send_json($response);
        }

        $_POST = $_POST['dataForm'];

        chown( $_POST['filename'], 'root' );
        chmod( $_POST['filename'], 0755 );

        $response = array( 
            'success'   => true, 
            'data'      => $_POST, 
            'message'   => __( 'Is Writeable!', 'tonjoo' ) 
        );

        wp_send_json($response);
    }

    function get_settings_sections() {
        $sections = array( 
            array( 
                'id' => 'tonjoo_pwa_offline_mode', 
                'title' => __( 'Offline Mode', 'tonjoo' ) 
            ), 
            array( 
                'id' => 'tonjoo_pwa_assets', 
                'title' => __( 'Assets', 'tonjoo' ) 
            ), 
            array( 
                'id' => 'tonjoo_pwa_manifest', 
                'title' => __( 'Add to Homescreen', 'tonjoo' ) 
            ), 
            array( 
                'id' => 'tonjoo_pwa_lazy_load', 
                'title' => __( 'LazyLoad', 'tonjoo' ) 
            ), 
            array( 
                'id' => 'tonjoo_pwa_permissions_status', 
                'title' => __( 'Status', 'tonjoo' ) 
            ) 
        );

        return $sections;
    }
    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'tonjoo_pwa_offline_mode' => array( 
                array( 
                    'name'              => 'status', 
                    'label'             => __( 'Status', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'options'           => array( 
                        'on'    => __( 'Enable', 'tonjoo' ), 
                        'off'   => __( 'Disable', 'tonjoo' ) 
                    ), 
                    'type'              => 'select' 
                ), 
                array( 
                    'name'              => 'offline_page', 
                    'label'             => __( 'Offline Page', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'size'              => 'pwa-editor', 
                    'type'              => 'textarea' 
                ) 
            ), 
            'tonjoo_pwa_assets' => array( 
                array( 
                    'name'              => 'status', 
                    'label'             => __( 'Status', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'options'           => array( 
                        'on'    => __( 'Enable', 'tonjoo' ), 
                        'off'   => __( 'Disable', 'tonjoo' ) 
                    ), 
                    'type'              => 'select' 
                ), 
                array( 
                    'name'              => 'pgcache_reject_uri', 
                    'label'             => __( 'Never Cache Following Page', 'tonjoo' ), 
                    'desc'              => __( 'Always ignore the specified pages / directories. Supports regular expressions. Must start and end with <code>/</code>. Example: <code>/wp-admin/</code>', 'tonjoo' ), 
                    'default'           => '/wp-admin/', 
                    'size'              => '', 
                    'type'              => 'textarea' 
                ) 
            ), 
            'tonjoo_pwa_manifest' => array( 
                array( 
                    'name'              => 'status', 
                    'label'             => __( 'Status', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'options'           => array( 
                        'on'    => __( 'Enable', 'tonjoo' ), 
                        'off'   => __( 'Disable', 'tonjoo' ) 
                    ), 
                    'type'              => 'select' 
                ), 
                array( 
                    'name'              => 'app_name', 
                    'label'             => __( 'App Name', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'default'           => get_bloginfo('name'), 
                    'type'              => 'text' 
                ), 
                array( 
                    'name'              => 'short_name', 
                    'label'             => __( 'Short Name', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'default'           => get_bloginfo('name'), 
                    'type'              => 'text' 
                ), 
                array( 
                    'name'              => 'app_description', 
                    'label'             => __( 'App Description', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'default'           => get_bloginfo('description'), 
                    'type'              => 'textarea' 
                ), 
                array( 
                    'name'              => 'start_url', 
                    'label'             => __( 'Start Url', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'default'           => get_bloginfo('url'), 
                    'type'              => 'text' 
                ), 
                array( 
                    'name'              => 'theme_color', 
                    'label'             => __( 'Theme Color', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'default'           => '#ffffff', 
                    'type'              => 'color' 
                ), 
                array( 
                    'name'              => 'background_color', 
                    'label'             => __( 'Background Color', 'tonjoo' ), 
                    'desc'              => __( 'Splash Background Color', 'tonjoo' ), 
                    'default'           => '#ffffff', 
                    'type'              => 'color' 
                ), 
                array( 
                    'name'              => 'logo_48', 
                    'label'             => __( 'Logo 48x48px', 'tonjoo' ), 
                    'desc'              => __( 'Size 48x48px (.png)', 'tonjoo' ), 
                    'type'              => 'file' 
                ), 
                array( 
                    'name'              => 'logo_96', 
                    'label'             => __( 'Logo 96x96', 'tonjoo' ), 
                    'desc'              => __( 'Size 96x96 (.png)', 'tonjoo' ), 
                    'type'              => 'file' 
                ), 
                array( 
                    'name'              => 'logo_128', 
                    'label'             => __( 'Logo 128x128', 'tonjoo' ), 
                    'desc'              => __( 'Size 128x128 (.png)', 'tonjoo' ), 
                    'type'              => 'file' 
                ), 
                array( 
                    'name'              => 'logo_144', 
                    'label'             => __( 'Logo 144x144', 'tonjoo' ), 
                    'desc'              => __( 'Size 144x144 (.png)', 'tonjoo' ), 
                    'type'              => 'file' 
                ), 
                array( 
                    'name'              => 'logo_152', 
                    'label'             => __( 'Logo 152x152', 'tonjoo' ), 
                    'desc'              => __( 'Size 152x152 (.png)', 'tonjoo' ), 
                    'type'              => 'file' 
                ), 
                array( 
                    'name'              => 'logo_192', 
                    'label'             => __( 'Logo 192x192', 'tonjoo' ), 
                    'desc'              => __( 'Size 192x192 (.png)', 'tonjoo' ), 
                    'type'              => 'file' 
                ), 
                array( 
                    'name'              => 'logo_256', 
                    'label'             => __( 'Logo 256x256', 'tonjoo' ), 
                    'desc'              => __( 'Size 256x256 (.png)', 'tonjoo' ), 
                    'type'              => 'file' 
                ), 
                array( 
                    'name'              => 'logo_384', 
                    'label'             => __( 'Logo 384x384', 'tonjoo' ), 
                    'desc'              => __( 'Size 384x384 (.png)', 'tonjoo' ), 
                    'type'              => 'file' 
                ), 
                array( 
                    'name'              => 'logo_512', 
                    'label'             => __( 'Logo 512x512', 'tonjoo' ), 
                    'desc'              => __( 'Size 512x512 (.png)', 'tonjoo' ), 
                    'type'              => 'file' 
                ), 
                array( 
                    'name'              => 'mobile_apps', 
                    'label'             => __( 'Mobile Apps', 'tonjoo' ), 
                    'desc'              => __( 'Your APP ID', 'tonjoo' ), 
                    'type'              => 'text' 
                ) 
            ), 
            'tonjoo_pwa_lazy_load' => array( 
                array( 
                    'name'              => 'status', 
                    'label'             => __( 'Status', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'options'           => array( 
                        'on'    => __( 'Enable', 'tonjoo' ), 
                        'off'   => __( 'Disable', 'tonjoo' ) 
                    ), 
                    'type'              => 'select' 
                ), 
                array( 
                    'name'              => 'preload_image', 
                    'label'             => __( 'Preload Image', 'tonjoo' ), 
                    'desc'              => __( 'Base64 Encode', 'tonjoo' ), 
                    'default'           => 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==', 
                    'type'              => 'text' 
                ), 
                array( 
                    'name'              => 'css_class', 
                    'label'             => __( 'CSS Class', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'default'           => 'pwa-image-responsive', 
                    'type'              => 'text' 
                ), 
                array( 
                    'name'              => 'root_margin', 
                    'label'             => __( 'Margin', 'tonjoo' ), 
                    'desc'              => __( 'Pixel Unit', 'tonjoo' ), 
                    'default'           => 0, 
                    'type'              => 'number' 
                ), 
                array( 
                    'name'              => 'threshold', 
                    'label'             => __( 'Threshold', 'tonjoo' ), 
                    'desc'              => __( '', 'tonjoo' ), 
                    'default'           => 0, 
                    'type'              => 'text' 
                ) 
            ), 
            'tonjoo_pwa_permissions_status' => array( 
                array( 
                    'name'              => 'permissions', 
                    'label'             => __( '', 'tonjoo' ), 
                    'type'              => 'permissions' 
                )
            )
        );

        return $settings_fields;
    }

	public function includes(){
		if ( !class_exists( 'TONJOO_PWA_SETTINGS' ) )
			include_once( 'settings.class.php' );
	}

}

return new TONJOO_PWA_ADMIN();
