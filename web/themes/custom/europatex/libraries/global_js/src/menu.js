(function ($) {
  'use strict';
  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  // main menu Behavior
  Drupal.behaviors.mainMenuBehavior = {
    attach: function (context, settings) {

      // $('#block-main-navigation > ul').first().attr('id', 'mainnavigation-ul');
      // $('#block-secondary-navigation > ul').attr('id', 'secondarynavigation-ul');

      var $mobileMenu = $('#mobile-menu');
      var $navLink = $mobileMenu.find('a');
      // If mobile menu button exists and is currently being displayed,
      // initialize outside of the tab order.
      var $toggleInit = $('#toggle-menu');
      var $searchContainer = $(context).find('.region-header-search');

      $mobileMenu.on('keydown', function (e) {
        if ($('body').hasClass('show-nav')) {
          var which = e.which;
          var target = e.target;

          var $focusableElements = $mobileMenu.find(':tabbable');
          var $firstFocusable = $focusableElements.first();
          var $lastFocusable = $focusableElements.last();
          var $toggle = $toggleInit;

          if (which === 9 && e.shiftKey) { // SHIFT + TAB
            // go to bottom of modal if going first focusable element

            if (target === $firstFocusable[0]) {
              e.preventDefault();
              $toggle.focus();
            }
          } else if (which === 9) { // TAB
            // go to top of modal if last focusable element
            if (target === $lastFocusable[0]) {
              e.preventDefault();
              $toggle.focus();
              // return false;
            }
          } else if (which === 27) { // ESCAPE
            // click the close button which hides the mobile menu and focuses its trigger
            $toggle.click();
          }
        }
      });


      $(window).resize(function () {
        if ($toggleInit.length && $toggleInit.css('display') === 'block') {
          $navLink.attr('tabindex', -1);
        } else {
          $navLink.removeAttr('tabindex', -1);
        }
      });

      // Toggle Main Menu Mobile
      $('#toggle-menu', context).once('mainMenuBehavior').click(function () {
        var $toggle = $(this);
        var $body = $('body');
        $('body').toggleClass('show-nav');
        if ($body.hasClass('show-nav')) {
          $body.removeClass('show-search');
          $toggle.attr('aria-expanded', 'true');
          $navLink.removeAttr('tabindex', -1);
          $('main').attr('aria-hidden', true);

        } else {
          $toggle.attr('aria-expanded', 'false');
          $navLink.attr('tabindex', -1);
          $('main').removeAttr('aria-hidden');
        }
        return false;
      });

      ///////////////////////////////////
      // SEARCH

      $('#toggle-search', context).once('mainMenuBehavior').click(function () {
        var $toggle = $(this);
        var $body = $('body');
        $('body').toggleClass('show-search');
        if ($body.hasClass('show-search')) {
          $body.removeClass('show-nav').removeClass('show-account');
          $toggleInit.attr('aria-expanded', 'false');
          $toggle.attr('aria-expanded', 'true');
          $navLink.removeAttr('tabindex', -1);
          $('main').attr('aria-hidden', true);
          $searchContainer.find('.form-text').focus().select();
        } else {
          $toggle.attr('aria-expanded', 'false');
          $('main').removeAttr('aria-hidden');
        }
        return false;
      }).focusout(function () {
        if ($('body').hasClass('show-search')) {
          $searchContainer.find('.form-text').focus();
        }
      });

      $('#toggle-search').on('keydown', function (e) {
        var $searchMenu = $(this);

        if ($('body').hasClass('show-search')) {
          var which = e.which;
          var target = e.target;

          var $focusableElements = $searchMenu.find(':tabbable');
          var $firstFocusable = $focusableElements.first();
          var $lastFocusable = $focusableElements.last();
          var $toggle = $searchToggleInit;

          if (which === 9 && e.shiftKey) { // SHIFT + TAB
            // go to bottom of modal if going first focusable element

            if (target === $firstFocusable[0]) {
              e.preventDefault();
              $toggle.focus();
            }
          } else if (which === 9) { // TAB
            // go to top of modal if last focusable element
            if (target === $lastFocusable[0]) {
              e.preventDefault();
              $toggle.focus();
              // return false;
            }
          } else if (which === 27) { // ESCAPE
            // click the close button which hides the mobile menu and focuses its trigger
            $toggle.click();
          }
        }
      });

    }
  };

  Drupal.behaviors.headerFlowBehavior = {
    attach: function (context) {
      // throttling variables
      var timeout = null;
      var delay = 20;

      var wait = function (afterward) {
        clearTimeout(timeout);
        timeout = setTimeout(afterward, delay);
      };

      $(context).find('header').once('headerFlowBehavior').each(function () {
        var $header = $(this);
        var $headerTop = $header.find('.header__top .secondarynavigation .menu, .header__top .useraccountmenu .menu');
        var $search = $header.find('.header__bottom .toggle-search');
        var $headerBottom = $header.find('.header__bottom');

        var updateMenuFlow = function () {
          if ($(window).width() < 960) {
            $search.appendTo($headerTop);
          }

          else {
            $search.appendTo($headerBottom);
          }
        };

        // Control flow of elements
        $(window).on('resize load',function () {
          wait(function() {
            updateMenuFlow();
          });
        });

        $header.each(function() {
          updateMenuFlow();
        });
      });
    }
  };

})(jQuery);
