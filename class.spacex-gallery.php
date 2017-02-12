<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Layout
include_once dirname( __FILE__ ) . '/view.spacex-gallery.php';

if ( ! class_exists( 'Space_X_Gallery' ) ) {
    
class Space_X_Gallery {
        
        /**
         * Message to display in the admin_notice
         * @var string
         */
        public $message = '';
        
        /**
         * Error to display in the admin_notice
         * @var string
         */
        public $error = '';
        
        private $name;
        private $version;
        private $shortcode;
                
        /**
         * Constructor. Initializes WordPress hooks
         */
        public function __construct() {
            add_action( 'print_media_templates', array( $this, 'spacex_media_templates' ) );
        }
        
        /**
         * Load language files
         * @action plugins_loaded
         */
        public static function plugin_textdomain() {
            // Note to self, the third argument must not be hardcoded, to account for relocated folders.
            load_plugin_textdomain( 'spacex-gallery', false, dirname( plugin_basename( SPACEX_GALLERY_FILE ) ) . '/languages' );
        }
        
        /**
         * Media UI integration
         */
        public function spacex_media_templates() {

            ?>
            <script type="text/html" id="tmpl-wc-gallery-settings">
                <label class="setting">
                    <span><?php _e( 'Make SpaceX Gallery', 'spacex-gallery' ); ?></span>
                    <input class="check-spacex" type="checkbox" name="check-spacex" data-setting="check-spacex">
            </script>
            <?php
        }

}

} // END if ( ! class_exists() )