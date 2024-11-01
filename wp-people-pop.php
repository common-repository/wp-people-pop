<?php
/**
 * Plugin Name: WP People Pop
 * Plugin URI: http://whoischris.com
 * Description: This plugin allows websites to create lists of people with profile information.  Useful for faculty, team listings, corporate leaders and so on.
 * Version: 1.5.1
 * Author: Chris Flannagan
 * Author URI: http://whoischris.com
 * License: GPL2
 */

if( !class_exists( 'WP_People_Pop' ) )
{
    class WP_People_Pop
    {
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // register actions
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			
			require_once(sprintf("%s/people/people_template.php", dirname(__FILE__)));
			$PostTypePeople = new PostTypePeople();
			
			require_once(sprintf("%s/people/people_shortcode.php", dirname(__FILE__)));
			add_action( 'wp_enqueue_scripts', array( &$this, 'pp_scripts' ) );
        } // END public function __construct
    
        /**
         * Activate the plugin
         */
        public static function activate()
        {
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */     
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate
		
		/**
		 * Enqueue Scripts
		 */
		public function pp_scripts() {
			// register shortcode
			$ShowList = new ShowList();
			add_shortcode( 'peoplepop', array( $ShowList, 'peoplepop_sc_func' ) );
		}
		
		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{
		} // END public static function activate
	}
} // END if(!class_exists('WP_People_Pop'));

// Add a link to the settings page onto the plugin page
if(class_exists('WP_People_Pop'))
{			
	// Installation and uninstallation hooks
	register_activation_hook( __FILE__, array('WP_People_Pop', 'activate') );
	register_deactivation_hook(__FILE__, array( 'WP_People_Pop', 'deactivate') );

	// instantiate the plugin class
	$WP_People_Pop = new WP_People_Pop();
}