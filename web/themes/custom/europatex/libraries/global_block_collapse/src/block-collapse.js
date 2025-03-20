((Drupal, once) => {

  Drupal.behaviors.collapseOnBlockMobile = {
    attach: (context, settings) => {
      const mobileBlocksQuery = window.matchMedia('(max-width: 30em)');

      mobileBlocksQuery.addEventListener('change', handleDeviceChange);

      function handleDeviceChange(mediaQuery) {
        // Mobile
        if (mediaQuery.matches) {
          handleMobileBlockCollapse();
        }
      }

      // Check media match on page load
      handleDeviceChange(mobileBlocksQuery);

      function handleMobileBlockCollapse() {
        var options = {
          duration: 200,
          easing: "ease-in-out",
          fill: "backwards",
          display: "block",
          overflow: "hidden"
        };
        var blockWrappingElements = once('collapseOnBlockMobile', document.getElementsByClassName('js-block-collapse'));

        for (var i = 0, len = blockWrappingElements.length; i < len; i++) {
          let wrapper = blockWrappingElements[i];
          let titleElement = wrapper.getElementsByClassName('block-title')[0];
          let targetElement = wrapper.getElementsByClassName('block-content')[0];
          let id = 'collapsed-content-' + i;

          // Hide content
          targetElement.style.display = "none";

          // Styling class
          wrapper.classList.add('has-block-collapse');

          // Aria
          titleElement.setAttribute('tabindex', '0');
          titleElement.setAttribute('role', 'button');
          titleElement.setAttribute('aria-labelledby',id);
          titleElement.setAttribute('aria-expanded', false);
          targetElement.setAttribute('aria-hidden', true);
          targetElement.setAttribute('id', id);

          /*
           * Click event
          */
          titleElement.addEventListener('click', (event) => {
            SlideElement.toggle(targetElement, options).then((opened) => {
              titleElement.setAttribute('aria-expanded', opened);
              targetElement.setAttribute('aria-hidden', !opened);
            });
          });

          /*
           * Keyup event (enter key pressed)...triggering click to leave logic in click handler
          */
          titleElement.addEventListener('keyup', (event) => {
            if (event.keyCode === 13) {
              titleElement.click();
            }
          });
        }
      }
    }
  }
})(Drupal, once)
