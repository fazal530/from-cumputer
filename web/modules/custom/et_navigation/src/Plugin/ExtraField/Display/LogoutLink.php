<?php

namespace Drupal\et_navigation\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Url;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;

/**
 * Display logout link on user page.
 *
 * @ExtraFieldDisplay(
 *   id = "et_navigation_logout_link",
 *   label = @Translation("Log out link"),
 *   description = @Translation("Prompt for users to log out."),
 *   bundles = {
 *     "user.*",
 *   },
 *   weight = 0,
 *   visible = false
 * )
 */
class LogoutLink extends ExtraFieldPlusDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $element = [
      '#cache' => [
        'contexts' => ['user'],
      ],
    ];
    $account = \Drupal::currentUser();
    if ($account->id() == $entity->id()) {
      $element['link'] = [
        '#type' => 'link',
        '#title' => $this->t('Log out'),
        '#url' => Url::fromRoute('user.logout'),
        '#attributes' => [
          'class' => ['button--primary'],
        ],
      ];
    }

    return $element;
  }

}
