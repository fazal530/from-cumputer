<?php

namespace Drupal\et_returns\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Redirect users to the Fedex return label site.
 */
class FedexRmaRedirect extends ControllerBase {

  /**
   * Perform the redirect.
   */
  public function performRedirect() {
    return new TrustedRedirectResponse('https://fulfillment.fedex.com/web/commerce/rma-web?customerId=2555655&token=eyJjdHkiOiJKV1QiLCJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..Cp51UdG_71lgo-xixXqUnw.4IB8hY8S5L6cYbyizIDl8a1ijZ6gpXWkNssrlwYipAQFg5KKydabscFfn6xNbNs7Uh668FhgXQfkDNwaKQeGv6bMvKSxtxHdlpohWVLgFAXApya8lFZ7lpI3FRQnFy083PjcmexqUMG8p7FjRz3Ake9E2flc98ubCEqZzjvYcnQK6_7pN3-iGmqlU224PRaIvXdJu0YTEod0LGs9RNg5eUtfKhkSZpocyJIA5T0Ega-WubUjzbeeoyDc3yELzoXQ4W3pgiV-xLU9ShEbPCkV970bXA0AXcKX2IIVh88e29pc5Jmi_YKC5IP9NWiFBOyL0wEdVEby7kgz0a_ftXqh4_svgOIj-xWZfWvdlVISlj-l-uFliWYU12YhRU9-pb62.tjlwF74D-qHU9LCYLNeO3A#');
  }

}
