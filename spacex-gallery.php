<?php
/* 
 * Plugin Name: SpaceX Gallery
 * Plugin URI: https://github.com/jekkilekki/plugin-spacex-gallery
 * Description: An infinitely scrolling gallery spanning multiple images across a page and inspired by SpaceX's Careers page.
 * Version: 0.0.1
 * Author: jekkilekki
 * Author URI: https://aaronsnowberger.com
 * License: GPLv2 or later
 * Text Domain: spacex-gallery
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2016 Aaron Snowberger
*/

if ( ! defined( 'WPINC' ) ) die;

define( 'SPACEX_GALLERY_VERSION',   '0.0.1' );
define( 'SPACEX_GALLERY_DIR',       plugin_dir_path( __FILE__ ) );
define( 'SPACEX_GALLERY_FILE',      __FILE__ );

require_once( SPACEX_GALLERY_DIR . 'class.spacex-gallery.php' );

register_activation_hook( __FILE__, array( 'Space_X_Gallery', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Space_X_Gallery', 'plugin_deactivation' ) );

$Space_X_Gallery = new Space_X_Gallery();