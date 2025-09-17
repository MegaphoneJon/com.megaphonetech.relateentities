<?php

/**
 * DAOs provide an OOP-style facade for reading and writing database records.
 *
 * DAOs are a primary source for metadata in older versions of CiviCRM (<5.74)
 * and are required for some subsystems (such as APIv3).
 *
 * This stub provides compatibility. It is not intended to be modified in a
 * substantive way. Property annotations may be added, but are not required.
 * @property string $id
 * @property string $entity_table_a
 * @property string $entity_id_a
 * @property string $entity_table_b
 * @property string $entity_id_b
 * @property string $relationship_type_id
 * @property bool|string $is_active
 * @property string $created_date
 * @property string $modified_date
 */
class CRM_RelateEntities_DAO_RelateEntities extends CRM_RelateEntities_DAO_Base {

  /**
   * Required by older versions of CiviCRM (<5.74).
   * @var string
   */
  public static $_tableName = 'civicrm_relate_entities';

}
