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
    return new OgSelectionHandler($field, $instance, $entity_type, $entity);
  }

  /**
   * Override EntityReferenceHandler::settingsForm().
   */
  public static function settingsForm($field, $instance) {
    $form = parent::settingsForm($field, $instance);
    $entity_type = $field['settings']['target_type'];
    $entity_info = entity_get_info($entity_type);

    $bundles = array();
    foreach ($entity_info['bundles'] as $bundle_name => $bundle_info) {
      if (og_is_group_type($entity_type, $bundle_name)) {
        $bundles[$bundle_name] = $bundle_info['label'];
      }
    }

    if (!$bundles) {
      $form['target_bundles'] = array(
        '#type' => 'item',
        '#title' => t('Target bundles'),
        '#markup' => t('Error: The selected "Target type" %entity does not have bundles that are a group type', array('%entity' => $entity_info['label'])),
      );
    }
    else {
      $settings = $field['settings']['handler_settings'];
      $settings += array(
        'target_bundles' => array(),
        'membership_type' => OG_MEMBERSHIP_TYPE_DEFAULT,
      );

      $form['target_bundles'] = array(
        '#type' => 'select',
        '#title' => t('Target bundles'),
        '#options' => $bundles,
        '#default_value' => $settings['target_bundles'],
        '#size' => 6,
        '#multiple' => TRUE,
        '#description' => t('The bundles of the entity type acting as group, that can be referenced. Optional, leave empty for all bundles.')
      );

      $options = array();
      foreach (og_membership_type_load() as $og_membership) {
        $options[$og_membership->name] = $og_membership->description;
      }
      $form['membership_type'] = array(
        '#type' => 'select',
        '#title' => t('OG membership type'),
        '#description' => t('Select the membership type that will be used for a subscribing user.'),
        '#options' => $options,
        '#default_value' => $settings['membership_type'],
        '#required' => TRUE,
      );
    }

    return $form;
  }

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
