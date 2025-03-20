(function ($) {
  'use strict';

  Drupal.behaviors.productFilterBehavior = {
    attach: function attach(context, settings) {

      $('.js-toggle-filters', context).once('productFilterBehavior').click(function () {
        $('body').toggleClass('show-filters');

        // @todo: handle focus / tabbing / accessibility
        // if ($body.hasClass('show-filters')) {
        //   $body.removeClass('show-search');
        //   $toggle.attr('aria-expanded', 'true');
        //   $navLink.removeAttr('tabindex', -1);
        //   $('main').attr('aria-hidden', true);
        // } else {
        //   $toggle.attr('aria-expanded', 'false');
        //   $navLink.attr('tabindex', -1);
        //   $('main').removeAttr('aria-hidden');
        // }

        return false;
      });
    }
  };
})(jQuery);
