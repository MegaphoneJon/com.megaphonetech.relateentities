<?php

class CRM_RelateEntities_Settings {

  public static function install() {
    CRM_Core_DAO::executeQuery("
      CREATE TABLE IF NOT EXISTS `civicrm_relate_entities` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `entity_table_a` varchar(255) NOT NULL,
        `entity_table_b` varchar(255) NOT NULL,
        `entity_id_a` int(10) UNSIGNED NOT NULL,
        `entity_id_b` int(10) UNSIGNED NOT NULL,
        `relationship_type_id` int(10) UNSIGNED NOT NULL,
        `is_active` tinyint(4) NOT NULL DEFAULT '1',
        `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `UI_civicrm_relate_entities_entity_table_a` (`entity_table_a`),
        KEY `UI_civicrm_relate_entities_entity_table_b` (`entity_table_b`),
        KEY `UI_civicrm_relate_entities_entity_id_a` (`entity_id_a`),
        KEY `UI_civicrm_relate_entities_entity_id_b` (`entity_id_b`),
        KEY `FK_civicrm_relate_entities_relationship_type_id` (`relationship_type_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ");
    CRM_Core_DAO::executeQuery("
      ALTER TABLE `civicrm_relate_entities`
        ADD CONSTRAINT `FK_civicrm_relate_entities_relationship_type_id` FOREIGN KEY (`relationship_type_id`) REFERENCES `civicrm_relationship_type` (`id`) ON DELETE CASCADE;
    ");
  }

  public static function uninstall() {
    CRM_Core_DAO::executeQuery("
      DROP TABLE IF EXISTS civicrm_relate_entities;
    ");
  }

}
