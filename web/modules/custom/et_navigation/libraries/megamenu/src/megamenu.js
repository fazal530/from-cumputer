/**
 * @file
 * Makes checkboxes interactive.
 */

 (function ($, Drupal) {

  'use strict';

  Drupal.behaviors.megamenu = {
    attach: function (context) {
      $('.megamenu', context).once('megamenu').each(function() {
        const $megamenu = $(this);
        $megamenu.accessibleMegaMenu({
          uuidPrefix: 'accessible-menu',
          menuClass: 'menu',
          topNavItemClass: 'top-level',
          panelClass: 'dropdown',
          panelGroupClass: 'sub-menu',
          hoverClass: 'hover',
          focusClass: 'focus',
          openClass: 'active'
        });
      });
    }
  };

})(jQuery, Drupal);
