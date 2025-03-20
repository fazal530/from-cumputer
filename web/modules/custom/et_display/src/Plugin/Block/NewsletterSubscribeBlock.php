<?php

namespace Drupal\et_display\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Url;

/**
 * Provides a 'NewsletterSubscribeBlock' block.
 *
 * @Block(
 *  id = "newsletter_subscribe",
 *  admin_label = @Translation("Subscribe to Newsletter"),
 * )
 */
class NewsletterSubscribeBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['body'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Body'),
      '#default_value' => isset($config['body']) ? $config['body'] : $this->t('Sign up for emails to be the first to know our new products!'),
    ];

    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
      // '#size' => 30,
      '#default_value' => isset($config['url']) ? $config['url'] : 'https://docs.google.com/forms/d/e/1FAIpQLSfIW-iSqrCwnaWFrGc2gEBv4VHu3r5YSNPCWYG9VukgFTlmiw/viewform',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['body'] = $values['body'];
    $this->configuration['url'] = $values['url'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $body = $this->configuration['body'];
    $url = $this->configuration['url'];

    $render = [];

    $render['body'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $body,
    ];

    $render['link'] = [
      '#type' => 'link',
      '#title' => $this->t('Subscribe'),
      '#url' => Url::fromUri($url),
      '#attributes' => [
        'class' => ['button--primary'],
      ],
    ];

    return $render;
  }

}
