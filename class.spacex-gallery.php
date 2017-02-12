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
        
        /** NEW
         * Stores the class instance.
         * 
         * @var Space_X_Gallery
         */
        private static $instance = null;
        
        /**
         * Returns the instance of this class. (Singleton)
         * 
         * @return Space_X_Gallery The instance
         */
        public static function get_instance() {
            if( ! self::$instance ) {
                self::$instance = new self;
            }
            return self::$instance;
        }
        
        /**
         * Initializes the plugin.
         */
        public function init_plugin() {
            $this->init_hooks();
        }
        
        /**
         * Initializes the WP actions.
         *  - admin_print_scripts
         */
        private function init_hooks() {
            add_action( 'wp_enqueue_media', array( $this, 'spacex_add_media_option' ) );
            add_action( 'print_media_templates', array( $this, 'spacex_media_templates' ) );
        }
                
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
         * Media UI integration (Enqueues the script)
         */
        public function spacex_add_media_option() {
            //add_action( 'admin_print_footer_scripts', array( $this, 'spacex_media_gallery_option' ) );
        
            /** NEW
             * From Dominik Shilling
             */
            if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
                return;
            
            wp_enqueue_script(
                    'spacex-gallery-setting',
                    plugins_url( 'js/spacex-gallery-setting.js', __FILE__ ),
                    array( 'media-views' )
            );
            
        }
        
        /**
         * Outputs the view template with the custom setting.
         */
        public function spacex_media_gallery_option() {
            
            if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
                return;
            
            ?>
            <!--<script type="text/javascript">-->
//            jQuery( function() {
//                if( wp.media.view.Settings.Gallery ) {
//                    wp.media.view.Settings.Gallery = wp.media.view.Settings.extend({
//                       className: 'collection-settings gallery-settings',
//                       template: wp.media.template( 'gallery-settings' ),
//                       render: function() {
//                           wp.media.View.prototype.render.apply( this, arguments );
//                           // Append our option
//                           var $s = this.$('select.size');
//                           //if( !$s.find( 'option[value="0"]' ).length ) {
//                               $s.append( '<label>SpaceX-ify!!!</label>' );
//                           //}
//                           // Select the correct values.
//                           _( this.model.attributes ).chain().keys().each( this.update, this );
//                           return this;
//                       }
//                    });
//                }
//            });
            <!--</script>-->
            
            <script type="text/html" id="tmpl-custom-gallery-setting">
                <label class="setting">
                    <span>Type</span>
                    <select class="type" name="type" data-setting="type">
                        <?php
                        
                        $types = array(
                            'default'   => __( 'SpaceX', 'spacex-gallery' )
                        );
                        
                        foreach ( $types as $value => $name ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'default' ); ?>>
                                <?php echo esc_html( $name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </script>
            
            <?php
        }
        
        public function spacex_media_templates() {
            if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
                return;

            ?>
            <script type="text/html" id="tmpl-spacex-gallery-setting">
                <label class="setting">
                    <span><?php _e( 'SpaceX Gallery', 'spacex-gallery' ); ?></span>
                    <input class="check-spacex" type="checkbox" name="check-spacex" data-setting="check-spacex">
                </label>
            </script>
            <?php
        }

}

} // END if ( ! class_exists() )