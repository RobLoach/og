<?php


/**
 * @file
 * OG example selection handler.
 */

class OgExampleSelectionHandler extends OgSelectionHandler {

  /**
   * Implements EntityReferenceHandler::getInstance().
   */
  public static function getInstance($field, $instance = NULL, $entity_type = NULL, $entity = NULL) {
    return new OgExampleSelectionHandler($field, $instance, $entity_type, $entity);
  }

  /**
   * Build an EntityFieldQuery to get referencable entities.
   */
  public function buildEntityFieldQuery($match = NULL, $match_operator = 'CONTAINS') {
    $group_type = $this->field['settings']['target_type'];


    if (empty($this->instance['field_mode']) || $group_type != 'node') {
      return parent::buildEntityFieldQuery($match, $match_operator);
    }

    $field_mode = $this->instance['field_mode'];
    // Show the user only the groups they belong to.
    $handler = EntityReference_SelectionHandler_Generic::getInstance($this->field, $this->instance, $this->entity_type, $this->entity);
    $query = $handler->buildEntityFieldQuery($match, $match_operator);

    if ($field_mode == 'default') {
      $query->propertyCondition('nid', '5', '<=');
    }
    else {
      $query->propertyCondition('nid', '5', '>');
    }

    return $query;
  }
}
