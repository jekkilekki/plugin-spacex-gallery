/**
 * SpaceX Gallery Settings
 */
( function( $ ) {
    "use strict";
    
    if ( 'undefined' == typeof( wp.media ) ) 
        return;
    
    var media = wp.media;
    
    // Wrap the render() function to append controls
    media.view.Settings.Gallery = media.view.Settings.Gallery.extend({
        render: function() {
            media.view.Settings.prototype.render.apply( this, arguments );
            
            // Append the custom template
            this.$el.append( media.template( 'spacex-gallery-setting' ) );
            
            // Save the setting
            media.gallery.defaults.spacex = false;
            media.gallery.defaults.smallspeed = '50000';
            media.gallery.defaults.largespeed = '90000';
            this.update.apply( this, ['spacex'] );
            this.update.apply( this, ['smallspeed'] );
            this.update.apply( this, ['largespeed'] );
            
            return this;
        }
    });
    
})( jQuery );