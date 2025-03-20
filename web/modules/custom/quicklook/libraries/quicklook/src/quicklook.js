/**
 * @file
 * Add quick look interaction.
 */

 (function ($, Drupal) {

  'use strict';


  const $pane = $('<div id="quicklook" class="quicklook"></div>');
  const $close = $('<button class="close-button" aria-controls="quicklook"><span>Close</span></button>').appendTo($pane);
  const $content = $('<div class="content"></div>').appendTo($pane);


  Drupal.behaviors.quicklook = {
    attach(context) {
      const elements = once('quicklook', '.quicklook-trigger', context);
      elements.forEach(initializeQuicklook);
    }
  };

  function initializeQuicklook(trigger) {
    const $trigger = $(trigger);
    const entityType = $trigger.data('entity-type');
    const entityId = $trigger.data('entity-id');
    const $grid = $trigger.closest('.views-content');
    const $gridItem = $trigger.closest('.views-row');

    $trigger.on('click', function(event) {
      event.preventDefault();

      $('.quicklook-selected').removeClass('quicklook-selected');
      $grid.addClass('quicklook-open');
      $gridItem.addClass('quicklook-selected');

      // Find last item in row.
      const itemTop = $gridItem.offset().top;
      let $lastItem = $gridItem;
      while ($lastItem.next().length && $lastItem.next().offset().top == itemTop) {
        $lastItem = $lastItem.next();
      }

      $pane.hide();
      $pane.insertAfter($lastItem);
      $content.html('<div class="loading"></div>');
      $pane.show();
      $('html, body').animate({scrollTop: $gridItem.offset().top});

      const ajaxObject = Drupal.ajax({
        url: '/quicklook/' + entityType + '/' + entityId,
        element: $content[0]
      });
      ajaxObject.execute();
    });

    $close.on('click', closeQuickLook);

    function closeQuickLook() {
      $('.quicklook-selected').removeClass('quicklook-selected');
      $grid.removeClass('quicklook-open');
      $pane.detach();
    }
  }

})(jQuery, Drupal);
