<?php
/**
 * Plugin Name: Disable HTTP Calls
 * Plugin URI: http://remicorson.com
 * Description: A plugin to disable WordPress HTTP calls for faster loading while working locally
 * Author: Remi Corson
 * Contributor: Remi Corson, corsonr
 * Author URI: http://remicorson.com/
 * Version: 1.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
class RC_Disable_HTTP_Calls {
 
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
    	add_action( 'admin_init', __CLASS__ . '::settings', 50 );
        add_action( 'in_admin_footer', __CLASS__ . '::topbar_css', 50 );
		add_action( 'admin_bar_menu', __CLASS__ . '::add_topbar_node', 999 );
		
		$action = get_option( 'http_calls', true );
		
		if( $action != false )
			add_filter( 'pre_http_request', '__return_true', 100 );
    }
 
     /**
     * Store Plugin settings
     *
     */
    public static function settings() {
		
		//avoid php notice: undefined index
		if( ! isset( $_GET['http_calls'] ) ) {
			return;
		}
		// Update value
		$action =  $_GET['http_calls'];
		
		if( $action == 'on' ) {
			update_option( 'http_calls', 1 );
		} elseif( $action == 'off' ) {
			update_option( 'http_calls', 0 );
		}
		
    }
    
    /**
     * Turns the nod in top bar into red to notify the plugin is on
     *
     */
    public static function topbar_css() {

		$action = get_option( 'http_calls', true );
		
		if( $action != false ) {
		?>
		<style>
			#wpadminbar #wp-admin-bar-http_calls { 
				background-color: #d00;
				background-image: -moz-linear-gradient(bottom, #f44, #d00 );
				background-image: -webkit-gradient(linear, left bottom, left top, from(#f44), to(#d00));
				right: 0 !important;
			}
			#wpadminbar #wp-admin-bar-http_calls:hover .ab-item { 
				background: none !important;
				color: #CCCCCC !important;
			}
		</style>
		<?php
		}
		
    }
    
    /**
     * Adds a new node to top bar
     *
     */
    public static function add_topbar_node() {

		global $wp_admin_bar;
		
		if ( !is_super_admin() || !is_admin_bar_showing() )
			return;
		
		$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
		$action = get_option( 'http_calls', true );
		
		if( $action == 1 ) {
			$href = ( strpos( $current_url, '?' ) !== false ? '&http_calls=off' : '?http_calls=off' );
		} else {
			$href = ( strpos( $current_url, '?' ) !== false ? '&http_calls=on' : '?http_calls=on' );
		}
		
		$action = get_option( 'http_calls', true );
		
		if( $action != false ) {
			$title = 'OFF';
		} else {
			$title = 'ON';
		}
		
		// Main Menu Item
		$args = array(
			'id'     => 'http_calls',
			'title'  => 'HTTPS Calls ' . $title,
			'parent' => 'top-secondary',
			'class'  => 'http_calls',
			'href'   => $current_url . $href
			);
			
		$wp_admin_bar->add_node( $args );
		
    }
 
}
 
RC_Disable_HTTP_Calls::init();