<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Layout
include_once dirname( __FILE__ ) . '/view.spacex-gallery.php';

if ( ! class_exists( 'Space_X_Gallery' ) ) {
    
class Space_X_Gallery {
        
        private $name;
        private $version;
        
        /**
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
            // Apply filter to default gallery shortcode
            //add_filter( 'post_gallery', array( $this::get_instance(), 'spacex_post_gallery' ), 10, 2 );
        }
        
        /**
         * Initializes the WP actions.
         *  - admin_print_scripts
         */
        private function init_hooks() {
            add_action( 'wp_enqueue_media', array( $this, 'spacex_add_media_settings' ) );
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
        public function spacex_add_media_settings() {

//            if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
//                return;
            
            wp_enqueue_script(
                    'spacex-gallery-settings',
                    plugins_url( 'js/spacex-gallery-settings.js', __FILE__ ),
                    array( 'media-views' )
            );
            
        }
        
        /**
         * Outputs the view template with the custom setting.
         */
        public function spacex_media_templates() {
            if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
                return;

            ?>
            <script type="text/html" id="tmpl-spacex-gallery-setting">
                <h2 style="margin-top: 1em; display: inline-block;"><br><?php _e( 'SpaceX Gallery Settings', 'spacex-gallery' ); ?></h2>
                
                <label class="setting">
                    <span><?php _e( 'Create SpaceX Gallery?', 'spacex-gallery' ); ?></span>
                    <input class="spacex" type="checkbox" name="spacex" data-setting="spacex">
                </label>
                
                <label class="setting">
                    <span><?php _e( 'Set small row speed (ms)', 'spacex-gallery' ); ?></span>
                    <input class="smallspeed" type="text" name="smallspeed" style="float:left;" data-setting="smallspeed" placeholder="50000">
                </label>
                
                <label class="setting">
                    <span><?php _e( 'Set Large row speed (ms)', 'spacex-gallery' ); ?></span>
                    <input class="largespeed" type="text" name="largespeed" style="float:left;" data-setting="largespeed" placeholder="90000">
                </label>
                
                <p style="color: #666; display: inline-block; font-style: italic">
                    <?php _e( 'Image sizes: The SpaceX Gallery creates 2 rows of images. The top row is smaller and automatically pulls `medium` images from the database. The bottom row is larger and pulls `large` images from the database.', 'spacex-gallery' ); ?>
                    <br>
                    <?php _e( 'Speed: Enter speeds in milliseconds.', 'spacex-gallery' ); ?>
                </p>
            </script>
            <?php
        }
        
        /**
         * The SpaceX Gallery Shortcode
         * 
         * This implements the functionality of the Gallery Shortcode 
         * for displaying WordPress images in a post.
         * 
         * @since 0.0.1
         * 
         * @param array $atts Attributes of the shortcode.
         * @return string HTML content to display gallery.
         */
        public function spacex_post_gallery( $output, $attr ) {
            
            // Initialize
            global $post, $wp_locale;
            $return = $output; // fallback
            
            // Gallery instance counter
            static $instance = 0;
            $instance++;
            
            // Validate the author's orderby attribute
            if ( isset( $attr['orderby'] ) ) {
                $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
                if( ! $attr['orderby'] ) unset( $attr['orderby'] );
            }
            
            // Get attributes from shortcode
            $atts = shortcode_atts( array(
                
                // Default WordPress attributes
                'order'         => 'ASC',
                'orderby'       => 'menu_order ID',
                'id'            => $post->ID,
                'itemtag'       => 'li',
                'icontag'       => 'figure',
                'captiontag'    => 'p',
                'captions'      => 'onhover',
                'captiontype'   => 'p',
                'columns'       => 3,
                'include'       => '',
                'exclude'       => '',
                'link'          => 'post',
                'size'          => 'medium',
                
                // SpaceX Attributes
                'spacex'        => 'true',
                'smallspeed'    => 50000,
                'largespeed'    => 90000,
                'gutter'        => '10',
                'overlap'       => '-10'
                
            ), $attr );
            
            // Initialize
            $id = intval( $atts['id'] );
            $attachments = array();
            if ( 'RAND' == $atts['order'] ) 
                $orderby = 'none';
            else 
                $orderby = $atts['orderby'];
            $include = $atts['include'];
            $exclude = $atts['exclude'];
            $order = $atts['order'];
            $size = $atts['size'];
            
            if ( ! empty( $atts['include'] ) ) {
                
                // If include attribute is present
                //$include = preg_replace( '/[^0-9,]+/', '', $include );
                $_attachments = get_posts( array( 
                    'include' => $include, 
                    'post_status' => 'inherit', 
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'order' => $order,
                    'orderby' => $orderby
                ) );
                
                // Setup attachments array
                foreach ( $_attachments as $key => $val ) {
                    $attachments[ $val->ID ] = $_attachments[ $key ];
                }
                
            } else if ( ! empty( $exclude ) ) {
                
                // If exclude attribute is present
                //$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
                
                // Setup attachments array
                $attachments = get_children( array( 
                    'post_parent' => $id,
                    'exclude'   => $exclude,
                    'post_status' => 'inherit', 
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'order' => $order,
                    'orderby' => $orderby
                ));
                
            } else {
                
                // If no include nor exclude attributes, setup attachments array
                $attachments = get_children( array( 
                    'post_parent' => $id,
                    'post_status' => 'inherit', 
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'order' => $order,
                    'orderby' => $orderby
                ));
                
            }
            
            if ( empty( $attachments ) ) return '';
            
            // Filter gallery differently for feeds
            if ( is_feed() ) {
                $output = "\n";
                foreach ( $attachments as $att_id => $attachment ) $output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
                return $output;
            }
            
            // Filter tags and attributes
            $itemtag = tag_escape( $atts['itemtag'] );
            $icontag = tag_escape( $atts['icontag'] );
            $captiontag = tag_escape( $atts['captiontag'] );
            
            $columns = intval( $atts['columns'] );
            $itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
            $float = is_rtl() ? 'right' : 'left';
            
            $selector = "gallery-{$instance}";
            
            
            
            $gallery_style = '<style>
/* Keyframes for Small Row */
@keyframes firstrun {
  0%    { left: 0; }
  100%  { left: -1440px; }
}
@keyframes slideshow {
  0%    { left: 1440px; }
  100%  { left: -1440px; }
}
/* Keyframes for Large Row */
@keyframes firstrunLG {
  0%    { left: 0; }
  100%  { left: -2880px; }
}
@keyframes slideshowLG {
  0%    { left: 2880px; }
  100%  { left: -2880px; }
}
</style>';
            $gallery_div = "<div id='$selector' class='spacex-gallery-wrapper gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size}'>";
            $output .= $gallery_style . $gallery_div . "<div class='spacex-gallery'><div id='spacex-cycle-1' class='cycle-slideshow simple-cycle photo-cycle-small' data-speed='60000'>";
            $output .= "<div class='cycle-group cycle-group-a'><ul class='photo-group'>";
            
            //$images = get_posts( $args );
            
            // Iterate through the attachments in this gallery instance
            $i = 0;
            foreach ( $attachments as $id => $attachment ) {
                
                // Attachment link
                $link = isset( $attr['link'] ) && 'file' == $attr['link'] ? wp_get_attachment_link( $id, $size, false, false ) : wp_get_attachment_link( $id, $size, true, false );
                
                // Begin itemtag
                $output .= "<{$itemtag} class='gallery-item'>";
                
                // icontag
                $output .= "
                <{$icontag} class='gallery-icon'>
                    $link 
                </{$icontag}>";
                
                if ( $captiontag && trim( $attachment->post_excerpt ) ) {
                    
                    // captiontag
                    $output .= "
                    <{$captiontag} class='gallery-caption'>
                        " . wptexturize( $attachment->post_excerpt ) . "
                    </{$captiontag}>";
                    
                }
                
                // End itemtag
                $output .= "</{$itemtag}>";
                
                // Line breaks by columns set
                if ( $columns > 0 && ++$i % $columns == 0 ) $output .= '<br style="clear: both;">';
                    
            }
            
            // End gallery output
            $output .= "
                <br style='clear: both;'>
            </ul></div></div></div></div>\n";
            
            return $output;
        }
        
        public function spacex_enqueue_scripts() {
            wp_enqueue_style('spacex-gallery-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), '170113', 'screen' );
            wp_enqueue_script('spacex-gallery-script', plugin_dir_url( __FILE__ ) . 'js/spacex-gallery-slider.js', array( 'jquery' ), '170113', true );
        }
        
} // END Space_X_Gallery

} // END if ( ! class_exists() )