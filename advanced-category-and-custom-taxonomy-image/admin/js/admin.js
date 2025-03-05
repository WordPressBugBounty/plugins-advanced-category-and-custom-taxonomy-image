jQuery( document ).ready( function( $ )
{
    $( '.advanced-category-and-custom-taxonomy-image-upload-btn' ).on( 'click', function ( event )
    {
        event.preventDefault();
        
        var self        = $( this );

        // Create the media frame.
        var file_frame  = wp.media.frames.file_frame = wp.media(
        {
            title: ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE.upload_tax_img_txt,
            button:
            {
                text: ADVANCED_CATEGORY_AND_CUSTOM_TAXONOMY_IMAGE.upload_txt,
            },
            multiple: false
        } );

        file_frame.on( 'select', function ()
        {
            attachment  = file_frame.state().get( 'selection' ).first().toJSON();
            
            self.prev( '.advanced-category-and-custom-taxonomy-image-url' ).val( attachment.url ).change();
        } );

        // Finally, open the modal
        file_frame.open();
    } );
} );