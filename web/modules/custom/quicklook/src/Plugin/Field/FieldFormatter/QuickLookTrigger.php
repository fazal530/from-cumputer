<?php

namespace Drupal\quicklook\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'quicklook_trigger' formatter.
 *
 * @FieldFormatter(
 *   id = "quicklook_trigger",
 *   label = @Translation("Quicklook trigger"),
 *   field_types = {
 *     "string",
 *     "uri",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class QuickLookTrigger extends StringFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    $entity = $items->getEntity();

    foreach ($elements as $delta => $element) {
      if ($element['#type'] == 'link') {
        $element['#attributes']['class'][] = 'quicklook-trigger';
        $element['#attributes']['data-entity-type'] = $entity->getEntityTypeId();
        $element['#attributes']['data-entity-id'] = $entity->id();
        $element['#attached']['library'][] = 'quicklook/quicklook';

        $elements[$delta] = $element;
      }
    }

    return $elements;
  }

}
