<?php

namespace Drupal\et_display\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'CopyrightBlock' block.
 *
 * @Block(
 *  id = "et_display_copyright",
 *  admin_label = @Translation("Copyright Text"),
 * )
 */
class CopyrightBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_year = date('Y');
    $copyright_text = $this->t('Copyright @current_year Europatex. All Rights Reserved.', ['@current_year' => $current_year]);

    $render = [];

    $render['body'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $copyright_text,
    ];

    return $render;
  }

}
