<?php

if ( ! defined( 'ABSPATH' ) ) exit;

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
                'spacex'        => 'false',
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
            $columns = intval( $atts['columns'] );
            
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
            
            echo '<pre>';
            var_dump( $attachments );
            echo '</pre>';
            
        /**
         * If 0 $attachments OR not set to a SpaceX Gallery, return
         * ---------------------------------------------------------------------
         */
            if ( empty( $attachments ) || $atts['spacex'] !== 'true' ) return '';
        /**
         * ---------------------------------------------------------------------
         */
            
            // Filter gallery differently for feeds
            if ( is_feed() ) {
                $output = "\n";
                foreach ( $attachments as $att_id => $attachment ) $output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
                return $output;
            }
            
        /**
         * Build Gallery Output
         * =====================================================================
         */
            
            // Gallery ID
            $selector = "gallery-{$instance}";
            
            // Check number of attachments
            $count = count( $attachments );
            $large = false;
            
            // If less than 10, only create a SMALL Gallery
            if ( $count > 10 ) { 
                $large = true; 
                array_chunk( $attachments, $count / 2 );
                $small_gallery = $attachments[0];
                $large_gallery = $attachments[1];
            } else {
                $small_gallery = $attachments;
                $large_gallery = array();
            }
            
            // If more than 5, split the array and be sure each side has at least 5 images
            if ( ! empty( $small_gallery ) ) { 
                // Make sure the array has at least ten items
                $small_gallery = $this->spacex_fill_cycle( $small_gallery );
                
                $small_gallery = array_chunk( $small_gallery, count( $small_gallery ) / 2 );
                $small_a = $small_gallery[0];
                $small_b = $small_gallery[1];
                
            }
            
            // If more than 5, split the array and be sure each side has at least 5 images
            if ( ! empty( $large_gallery ) ) { 
                // Make sure the array has at least ten items
                $large_gallery = $this->spacex_fill_cycle( $large_gallery );
                
                $large_gallery = array_chunk( $large_gallery, count( $large_gallery ) / 2 );
                $large_a = $large_gallery[0];
                $large_b = $large_gallery[1];

            }
            
            $small = $large ? '' : ' spacex-gallery-small';
            
            // Gallery Wrapper
            $output = "
<div id='$selector' class='spacex-gallery-wrapper{$small} gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size}'>
    <div class='spacex-gallery{$small}'>
        <div id='spacex-cycle-1' class='cycle-slideshow simple-cycle photo-cycle-small' data-speed='{$atts['smallspeed']}'>
            <div class='cycle-group cycle-group-a'>
                <ul class='photo-group'>";
            
                    $output .= $this->spacex_build_gallery_cycle( $small_a, $atts );
                    
                    $output .= "
                </ul>
            </div><!-- .cycle-group-a -->
            <div class='cycle-group cycle-group-b'>
                <ul class='photo-group'>";
            
                    $output .= $this->spacex_build_gallery_cycle( $small_b, $atts );
                    
                $output .= "
                </ul>
            </div><!-- .cycle-group-b -->
        </div><!-- .photo-cycle-small -->";
                
            if( ! empty( $large_gallery ) ) :
                
        $output .= "<div id='spacex-cycle-1' class='cycle-slideshow simple-cycle photo-cycle-large' data-speed='{$atts['largespeed']}'>
            <div class='cycle-group cycle-group-a'>
                <ul class='photo-group'>";
            
                    $output .= $this->spacex_build_gallery_cycle( $large_a, $atts );
                    
                    $output .= "
                </ul>
            </div><!-- .cycle-group-a -->
            <div class='cycle-group cycle-group-b'>
                <ul class='photo-group'>";
            
                    $output .= $this->spacex_build_gallery_cycle( $large_b, $atts );
                    
                $output .= "
                </ul>
            </div><!-- .cycle-group-b -->
        </div><!-- .photo-cycle-large -->";        
                
            endif;
      
            
            // End gallery output
            $output .= "
        <br style='clear: both;'>
    </div><!-- .spacex-gallery -->
</div><!-- .spacex-gallery-wrapper -->\n";
            
            
            return $output;
            
        }
        
        /**
         * Enqueue Scripts function
         * 
         * Enqueues the styles and scripts for the plugin
         */
        public function spacex_enqueue_scripts() {
            wp_enqueue_style('spacex-gallery-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), '170113', 'screen' );
            wp_enqueue_script('spacex-gallery-script', plugin_dir_url( __FILE__ ) . 'js/spacex-gallery-slider.js', array( 'jquery' ), '170113', true );
        }
        
        /**
         * Fill Photo Cycle Function
         * 
         * Fills each array with at least 12 items. These are then split into 
         * A and B which are the actual "moving parts."
         * 
         * @param array $array  The array of attachments to be sure there are at least 12 of
         * @param int   $count  The cut off limit for number of elements (defaults to 13 - so we return 12 by default)
         * @return array        The FILLED (and/or sliced) array that contains ONLY 12 items (or the number we specify) 
         */
        public function spacex_fill_cycle( $array, $count = 13 ) {
            // Make sure the first half contains at least 5 elements
            while ( count( $array ) < $count ) {
                $array = array_merge( $array, $array );
            }
            if ( count( $array ) > $count ) {
                $array = array_slice( $array, 0, $count );
            }
            return $array;
        }
        
        /**
         * Build Gallery Cycle
         * 
         * Function that takes our attachments array and attributes and builds 
         * our HTML output for display on the page.
         * 
         * @param array $attachments    The (filled) array of images
         * @param array $atts           The attributes used to describe our HTML and image element structure
         * @return String               The HTML that is output to the screen
         */
        public function spacex_build_gallery_cycle( $attachments, $atts ) {
            
            $size = $atts['size'];
            
            // Filter tags and attributes
            $itemtag = tag_escape( $atts['itemtag'] );
            $icontag = tag_escape( $atts['icontag'] );
            $captiontag = tag_escape( $atts['captiontag'] );
            
            $columns = intval( $atts['columns'] );
            $itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
            $float = is_rtl() ? 'right' : 'left';  
            
            
            $output = '';
            
            // Iterate through the attachments in this gallery instance
            $i = 0;
            foreach ( $attachments as $id => $attachment ) {
                
                // Attachment link
                //$link = isset( $atts['guid'] ) /*&& 'file' == $atts['link']*/ ? wp_get_attachment_link( $id, $size, false, false ) : wp_get_attachment_link( $id, $size, true, false );
//                $link = wp_get_attachment_link( $id, 'medium' );
                
                // Begin itemtag
                $output .= "<{$itemtag} class='photo-item'>";
                
                $output .= "<img class='gallery-photo' src='{$attachment->guid}'>";
                
                // End itemtag
                $output .= "</{$itemtag}>";
                
                // Line breaks by columns set
                //if ( $columns > 0 && ++$i % $columns == 0 ) $output .= '<br style="clear: both;">';
                    
            }
            
            return $output;
        }
        
} // END Space_X_Gallery

} // END if ( ! class_exists() )