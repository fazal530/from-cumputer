(function ($) {
  'use strict';

  Drupal.behaviors.dialogBehavior = {
    attach: function (context) {
      console.log('dialog');
      $(context).find('.dialog').once('dialogBehavior').each(function() {
        var $dialogContainer = $(this);
        var $button = $dialogContainer.find('.dialog__open');
        var $dialog = $dialogContainer.find('.dialog__content');
        var hasFrame = $dialogContainer.find('iframe').length;
        $button.accessibleModal({
          $dialog: $dialog,
          $closeButton: $dialogContainer.find('.dialog__close'),
          $openButton: $button,
          $frame: hasFrame ? $dialog.find('iframe') : null,
        });
      });
    }
  };

  /**
   * Set up an accessible modal dialog.
   */
  jQuery.fn.accessibleModal = function(opts) {
    var defaults = {
      $dialog: this.find('.dialog__content'),
      $closeButton: this.find('.dialog__close'),
      $page: $('#page'),
      $openButton: this.find('.dialog__open'),
      $frame: null
    };
    var options = $.extend(defaults, opts);

    var $prevFocusPosition = $(document.activeElement);
    options.$dialog.hide();

    options.$openButton.on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      openDialog();
    });

    if (options.$closeButton) {
      options.$closeButton.on('click', function (e) {
        e.preventDefault();
        closeDialog();
      });
    }

    /**
     * Open the modal dialog.
     */
    function openDialog() {
      options.$dialog.detach();
      options.$page.append(options.$dialog);
      options.$dialog.show();
      options.$closeButton.attr('tabindex', '0');

      // Store the previously active element for closeModal use.
      $prevFocusPosition = $(document.activeElement);

      $('a[href], button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])').each(function() {
        $(this).attr('data-tabindex', '0').attr('tabindex', -1);
      });
      options.$dialog.find('[data-tabindex]').each(function() {
        $(this).attr('tabindex', $(this).data('tabindex'));
      });

      $('body').addClass('no-scroll');

      // Hide page content to screen reader.
      options.$page.attr('aria-hidden', 'true');

      // Focus first tabbale element in dialog.
      options.$dialog.find('a[href], button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])').first().focus().addClass('first-focus').blur(function () {
        $(this).removeClass('first-focus');
      });

      // Clicking outside of the modal while the modal is
      // open will close the dialog and focus the trigger.
      $(document).on('click.accessibleModal', function (e) {
        if (!$(e.target).closest(options.$dialog.find('.dialog__container')).length) {
          e.preventDefault();
          closeDialog();
        }
        console.log('document clicked');
      });

      // If there is a video, play it.
      if (options.$frame) {
        var videoSrc = options.$frame.attr('src');
        options.$frame.attr('src', videoSrc.replace(/autoplay=0/, 'autoplay=1'));
        options.$frame.attr('tabindex', 0);
        options.$frame.focus();
      }

      // Trap keyboard events within the modal dialog.
      $(document).on('keydown.accessibleModal', function (e) {
        if (e.which === 27) {
          // Escape key was pressed.
          e.preventDefault();
          closeDialog();
          console.log('escape key pressed');
        }
        console.log('key pressed');
      });
    }

    /**
     * Close the modal dialog.
     */
    function closeDialog() {
      options.$dialog.removeAttr('aria-labelledby');

      // Stop any videos currently playing in the dialog.
      if (options.$frame) {
        var videoSrc = options.$frame.attr('src');
        options.$frame.attr('src', '');
        options.$frame.attr('src', videoSrc.replace(/autoplay=1/, 'autoplay=0'));
      }

      $('[data-tabindex]').each(function() {
        $(this).attr('tabindex', $(this).data('tabindex'));
      });

      $prevFocusPosition.focus();
      $('body').removeClass('no-scroll');
      options.$dialog.hide();
      options.$page.removeAttr('aria-hidden');

      $(document).off('click.accessibleModal');
      $(document).off('keydown.accessibleModal');
    }

  };

})(jQuery, Drupal, this, this.document);
