<?php

if ( ! function_exists('pwa_get_home_url') ) {
	// get url home
	function pwa_get_home_url() {
		$home_url = home_url();
		// check if plugin WPML Multilingual CMS is active
		if ( in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ), true ) ) {
			if ( function_exists('icl_object_id') ) {
				$my_default_lang 	= apply_filters('wpml_default_language', NULL );
        		$home_url 			= apply_filters( 'wpml_permalink', home_url(), $my_default_lang ); 
			}
		} else if ( in_array( 'polylang/polylang.php', apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ), true ) ) { //check if plugin polylang is active
			// nothing
		}
		return $home_url;
	}
}
