/**
 * @file
 * Cloudinary upload behaviors.
 */
 (function ($, Drupal) {

  'use strict';

  Drupal.behaviors.cloudinaryUpload = {
    attach: function (context, settings) {

      $(context).find('.cloudinary-button').once('cloudinaryUpload').each(function() {
        const $button = $(this);
        const $field = $('#' + $button.data('field'));

        const uploadWidget = cloudinary.createUploadWidget({
          cloudName: settings.cloudinary.cloudName,
          uploadPreset: 'unsigned'}, (error, result) => {
            if (!error && result && result.event === "success") {
              $field.val(result.info.public_id);
              $field.siblings('.cloudinary-preview').attr('src', 'https://res.cloudinary.com/' + settings.cloudinary.cloudName + '/image/upload/t_media_lib_thumb/v1/' + result.info.public_id + '.jpg');
            }
          }
        )

        $button.on('click', function(e) {
          e.preventDefault();
          uploadWidget.open();
        });
      });

    }
  };

} (jQuery, Drupal));
