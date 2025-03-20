/**
 * @file
 * Makes checkboxes interactive.
 */

 (function ($, Drupal) {

  'use strict';

  Drupal.facets = Drupal.facets || {};
  Drupal.behaviors.collapsedCheckboxes = {
    attach: function (context) {
      var $checkboxWidgets = $('.js-facets-collapsed-checkboxes', context)
        .once('facets-collapsed-checkbox');

      // $checkboxWidgets.find('input').on('change.facets', function (e) {
      //   e.preventDefault();

      //   var $widget = $(this).closest('.js-facets-widget');

      //   $widget.trigger('facets_filter', [ $(this).data('facetsredir') ]);
      // });

    }
  };

})(jQuery, Drupal);
