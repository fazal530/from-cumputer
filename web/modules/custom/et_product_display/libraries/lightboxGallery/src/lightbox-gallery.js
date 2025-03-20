(function ($) {
  'use strict';
  Drupal.behaviors.lightboxGalleryBehavior = {
    attach: function (context, settings) {
      $(context).find('body').once('lightboxGalleryBehavior').each(function () {
        const prvs = new Parvus();
      })
    }
  };

})(jQuery, Drupal, this, this.document);
