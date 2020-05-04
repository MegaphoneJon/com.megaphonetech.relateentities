<?php
/**
 * This api exposes CiviCRM RelateEntities.
 *
 * @package CiviCRM_APIv3
 */

/**
 * Save a RelateEntities.
 *
 * @param array $params
 *
 * @return array
 */
function civicrm_api3_relate_entities_create($params) {
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params, 'RelateEntities');
}

/**
 * Get a RelateEntities.
 *
 * @param array $params
 *
 * @return array
 *   Array of retrieved RelateEntities property values.
 */
function civicrm_api3_relate_entities_get($params) {
  return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * Delete a RelateEntities.
 *
 * @param array $params
 *
 * @return array
 *   Array of deleted values.
 */
function civicrm_api3_relate_entities_delete($params) {
  return _civicrm_api3_basic_delete(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}
