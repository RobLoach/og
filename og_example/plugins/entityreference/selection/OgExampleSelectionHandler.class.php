<?php


/**
 * @file
 * OG example selection handler.
 */

class OgExampleSelectionHandler extends OgSelectionHandler {

  /**
   * Build an EntityFieldQuery to get referencable entities.
   */
  public function buildEntityFieldQuery($match = NULL, $match_operator = 'CONTAINS') {
    $group_type = $this->field['settings']['target_type'];
    $field_mode = $this->instance['field_mode'];
    // Show the user only the groups they belong to.
    if ($field_mode == 'default' && $group_type == 'node') {
      $handler = EntityReference_SelectionHandler_Generic::getInstance($this->field, $this->instance, $this->entity_type, $this->entity);
      $query = $handler->buildEntityFieldQuery($match, $match_operator);
      $query->propertyCondition('title', 'a', 'CONTAINS');
    }
    else {
      $query = parent::buildEntityFieldQuery($match, $match_operator);
    }

    return $query;
  }
}
