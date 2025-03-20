<?php

namespace Drupal\et_search\Plugin\facets_pretty_paths\coder;

use Drupal\facets_pretty_paths\Plugin\facets_pretty_paths\coder\TaxonomyTermCoder;
use Drupal\taxonomy\Entity\Term;

/**
 * Banana facets pretty paths coder.
 *
 * @FacetsPrettyPathsCoder(
 *   id = "taxonomy_machine_name_coder",
 *   label = @Translation("Taxonomy machine name"),
 *   description = @Translation("Use term machine name, e.g. /color/<strong>blue</strong>")
 * )
 */
class TaxonomyMachineNameCoder extends TaxonomyTermCoder {

  /**
   * Encode an id into an alias.
   *
   * @param string $id
   *   An entity id.
   *
   * @return string
   *   An alias.
   */
  public function encode($id) {
    if ($term = Term::load($id)) {
      if (!empty($term->get('machine_name')->value)) {
        return $term->get('machine_name')->value;
      }
    }
    return $id;
  }

  /**
   * Decodes an alias back to an id.
   *
   * @param string $alias
   *   An alias.
   *
   * @return string
   *   An id.
   */
  public function decode($alias) {
    $termStorage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $query = $termStorage->getQuery()
      ->condition('vid', $this->configuration['facet']->id())
      ->condition('machine_name', $alias);
    $term_ids = $query->execute();
    if (!empty($term_ids)) {
      return reset($term_ids);
    }

    return $alias;
  }

}
