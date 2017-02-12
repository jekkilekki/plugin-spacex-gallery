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
            add_action( 'wp_enqueue_media', array( $this, 'spacex_add_media_option' ) );
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
        public function spacex_add_media_option() {
            add_action( 'admin_print_footer_scripts', array( $this, 'spacex_media_gallery_option' ) );
        }
        
        public function spacex_media_gallery_option() {
            ?>
            <script type="text/javascript">
            jQuery( function() {
                if( wp.media.view.Settings.Gallery ) {
                    wp.media.view.Settings.Gallery = wp.media.view.Settings.extend({
                       className: 'collection-settings gallery-settings',
                       template: wp.media.template( 'gallery-settings' ),
                       render: function() {
                           wp.media.View.prototype.render.apply( this, arguments );
                           // Append our option
                           var $s = this.$('select.size');
                           //if( !$s.find( 'option[value="0"]' ).length ) {
                               $s.append( '<label>SpaceX-ify!!!</label>' );
                           //}
                           // Select the correct values.
                           _( this.model.attributes ).chain().keys().each( this.update, this );
                           return this;
                       }
                    });
                }
            });
            </script>
            <?php
        }
        
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