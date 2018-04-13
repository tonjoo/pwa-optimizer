<?php 
/**
 * TONJOO_PWA_SETTINGS Class.
 *
 * Setting Options Class.
 *
 * @class       TONJOO_PWA_SETTINGS
 * @version     1.0
 * @author ebenhaezerbm <eben@tonjoo.com>
 */

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

class TONJOO_PWA_SETTINGS {
    /**
    * Singleton method
    *
    * @return self
    */
    public static function init() { 
        static $instance = false;

        if ( ! $instance ) { 
            $instance = new TONJOO_PWA_SETTINGS();
        }

        return $instance;
    }

    /**
     * Constructor
     */
    public function __construct(){
        $plugin_basename = TONJOO_PWA_PLUGIN_BASENAME;
        add_filter( "plugin_action_links_$plugin_basename", array($this, "plugin_setting_link") );

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

        add_action( 'admin_menu', array($this, 'admin_menu') );

        add_action( 'admin_notices', array($this, 'admin_notices') );

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

    /**
     * Enqueue scripts and styles
     */
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

            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_media();

            wp_enqueue_script( 'ace-monokai' );
            // wp_enqueue_style( 'bootstrap-switch' );
            // wp_enqueue_script( 'bootstrap-switch' );

            wp_enqueue_style( 'tonjoo-pwa' );
            wp_enqueue_script( 'tonjoo-pwa' );
        }
    }

    function admin_menu() {
        add_options_page( __( 'PWA Optimizer', 'tonjoo' ), __( 'PWA Optimizer', 'tonjoo' ), 'manage_options', TONJOO_PWA_SLUG, array($this, 'admin_opt') );
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

    function admin_opt() {
        if( isset($_POST['submit']) ){
            $this->update();
        }

        include( 'page-handler.php' );
    }

    function admin_notices() {
        global $pagenow;

        if( $pagenow != 'options-general.php' ) return;

        if ( ! isset($_GET['page']) || $_GET['page'] != 'pwa_optimizer' ) return;

        if( ! isset($_POST['submit']) ) return;

        try {

            if ( !isset($_POST['pwa_nonce']) || !wp_verify_nonce($_POST['pwa_nonce'], 'pwa-optimizer') ){
                throw new Exception( __( 'Something went wrong.', 'tonjoo' ) );
            }

            $class = 'success';
            $message = __( 'Settings saved.', 'tonjoo' );
        } catch (Exception $e) {
            $class = 'error';
            $message = $e->getMessage();
        }

        ?>

        <div id="message" class="updated notice notice-<?php echo $class; ?> is-dismissible">
            <p><?php echo $message; ?></p>
        </div>
        <?php
    }

    function update() {
        global $pagenow;

        if( 'options-general.php' != $pagenow ) return;

        if ( ! isset($_GET['page']) || $_GET['page'] != 'pwa_optimizer' ) return;

        if ( !isset($_POST['pwa_nonce']) || !wp_verify_nonce($_POST['pwa_nonce'], 'pwa-optimizer') ) return;

        if( ! isset($_POST['pwa_optimizer']) ) return;

        if( isset($_POST['pwa_optimizer']['offline_mode']['offline_page']) ){
            $_POST['pwa_optimizer']['offline_mode']['offline_page'] = stripslashes( $_POST['pwa_optimizer']['offline_mode']['offline_page'] );
        }

        // echo "<pre>";
        // print_r( $_POST['pwa_optimizer'] );
        // echo "</pre>";
        // exit();

        update_option( 'pwa_optimizer', $_POST['pwa_optimizer'] );
    }
}

TONJOO_PWA_SETTINGS::init();