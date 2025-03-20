<?php

namespace Drupal\et_product_display\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Url;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;

/**
 * Prompt for users to log in to see prices.
 *
 * @ExtraFieldDisplay(
 *   id = "et_product_display_login_prompt",
 *   label = @Translation("Login Prompt"),
 *   description = @Translation("Prompt for users to log in to see prices."),
 *   bundles = {
 *     "commerce_product.*",
 *   },
 *   weight = 0,
 *   visible = false
 * )
 */
class LoginPrompt extends ExtraFieldPlusDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $element = [
      '#cache' => [
        'contexts' => ['user.permissions'],
      ],
    ];
    $account = \Drupal::currentUser();
    if ($account->isAnonymous()) {
      $element['wrapper'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'class' => ['login-prompt'],
        ],
        'message_wrapper' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => [
            'class' => ['login-message'],
          ],
          'message' => [
            '#type' => 'html_tag',
            '#tag' => 'p',
            '#value' => $this->t('Please log in to view price information'),
          ],
        ],
        'login_wrapper' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => [
            'class' => ['login-actions'],
          ],
          'login' => [
            '#type' => 'link',
            '#title' => $this->t('Log in'),
            '#url' => Url::fromRoute('user.login'),
            '#attributes' => [
              'class' => ['button--primary'],
            ],
          ],
        ],
      ];
    }

    return $element;
  }

}
