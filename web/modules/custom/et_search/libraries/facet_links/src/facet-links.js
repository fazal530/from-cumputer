/**
 * @file
 * Enable facet links block.
 */

 (function ($, Drupal) {

  'use strict';

  Drupal.behaviors.facetLinks = {
    attach: function (context, settings) {
      $('.facet-links-content', context).once('facetLinks').each(function() {
        const $div = $(this);
        const ajaxSettings = {
          url: $div.data('url'),
          element: $div[0]
        };
        const ajaxObject = Drupal.ajax(ajaxSettings);
        ajaxObject.execute();
      });
    }
  };

})(jQuery, Drupal);
