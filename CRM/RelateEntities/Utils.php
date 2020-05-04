<?php

class CRM_RelateEntities_Utils {
  public static $_entityRefs = [];

  public static function getList() {
    $requiredParameters = [
      'entityId' => 'Integer',
      'entityTable' => 'String',
      'entityTableB' => 'String',
    ];

    $optionalParameters = [];

    $params = CRM_Core_Page_AJAX::defaultSortAndPagerParams();
    $params += CRM_Core_Page_AJAX::validateParams($requiredParameters, $optionalParameters);
    $params['offset'] = ($params['page'] - 1) * $params['rp'];
    $params['rowCount'] = $params['rp'];
    $params['sort'] = CRM_Utils_Array::value('sortBy', $params);

    $relatedEntities = self::getrelatedEntitiesSelector($params);
    CRM_Utils_JSON::output($relatedEntities);
  }

  public static function getrelatedEntitiesSelector($params) {

    $total = self::getRelatedEntities(
      $params['entityTable'], $params['entityId'], TRUE, TRUE,
      $params['entityTableB']
    );

    $results = self::getRelatedEntities(
      $params['entityTable'], $params['entityId'], TRUE, FALSE,
      $params['entityTableB']
    );
    $relatedEntities = [];
    $links = new CRM_RelateEntities_Page_Tab();
    $links = $links->links();
    while ($results->fetch()) {
      $action = CRM_Core_Action::UPDATE + CRM_Core_Action::DELETE + CRM_Core_Action::ADD;
      $class = ' crm-entity ';
      if ($results->is_active) {
        $action += CRM_Core_Action::DISABLE;
      }
      else {
        $class .= ' disabled';
        $action += CRM_Core_Action::ENABLE;
      }
      $relatedEntity = [
        'DT_RowId' => $results->id,
        'DT_RowClass' => $class,
        'DT_RowAttr' => [
          'data-entity' => 'RelateEntities',
          'data-id' => $results->id,
        ],
        'name' => self::buildName($results->entity_id, $params['entityTableB']),
        'is_active' => $results->is_active ? ts('Yes') : ts('No'),
        'relationship_name' => $results->relationship_name,
      ];
      $relatedEntity['links'] = CRM_Core_Action::formLink(
        $links,
        $action,
        ['id' => $results->id, 'entityTable' => $params['entityTable']],
        ts('more'),
        FALSE,
        'relatedentity.manage.action',
        'RelatedEntity',
        $results->id
      );
      array_push($relatedEntities, $relatedEntity);
    }
    $relatedEntitiesDT = [];
    $relatedEntitiesDT['data'] = $relatedEntities;
    $relatedEntitiesDT['recordsTotal'] = $total;
    $relatedEntitiesDT['recordsFiltered'] = $total;
    return $relatedEntitiesDT;
  }

  public static function buildName($entityId, $entityTable) {
    $name = '';
    $createLink = TRUE;
    try {
      switch ($entityTable) {
        case 'Contact':
          $return = 'sort_name';
          $url = 'civicrm/contact/view';
          $q = "reset=1&cid=$entityId";
          break;
        case 'FinancialType':
          $return = 'name';
          $url = 'civicrm/admin/financial/financialType';
          $q = "action=update&id={$entityId}&reset=1";
          break;
        case 'MembershipType':
          $return = 'name';
          $url = 'civicrm/admin/member/membershipType';
          $q = "action=update&id={$entityId}&reset=1";
          break;
      }
      $name = civicrm_api3($entityTable, 'getvalue', ['id' => $entityId, 'return' => $return]);
      if ($createLink) {
        $name = CRM_Utils_System::href($name, $url, $q);
      }
    }
    catch (Exception $e) {
    }

    return $name;
  }

  public static function buildEntitiesList(&$form, $entityTable, $entityId) {
    $results = self::getRelatedEntities($entityTable, $entityId, TRUE, FALSE, NULL, TRUE);
    $relatedEntities = [];
    while ($results->fetch()) {
      $entityRef = self::getEntityTable($results->entity_table, TRUE);
      $relatedEntities[$entityRef] = [
        'label' => preg_replace('/(?<!\ )[A-Z]/', ' $0', $entityRef),
        'columns' => self::getColumnHeaders($entityTable, $entityRef),
      ];
    }
    $form->assign('entityTable', $entityTable);
    $form->assign('entityId', $entityId);
    $form->assign('relatedEntities', $relatedEntities);
  }

  public function getColumnHeaders($entityTable, $entityRef) {
    return [
      'relationship_name' => ts('Relationship Type'),
      'name' => ts('Title'),
      'is_active' => ts('Is Active?'),
    ];
  }

  public static function getEntityTable($entityTable, $returnEntityRef = FALSE) {
    if (empty($entityTable)) {
      return;
    }

    if (empty(self::$_entityRefs)) {
      $entityTypesClasses = array_flip(CRM_Core_DAO_AllCoreTables::daoToClass());
      $entityTablesClasses = array_flip(CRM_Core_DAO_AllCoreTables::tables());
      self::$_entityRefs = array_combine($entityTablesClasses, $entityTypesClasses);
    }

    if ($returnEntityRef) {
      return CRM_Utils_Array::value($entityTable, self::$_entityRefs);
    }

    return array_search($entityTable, self::$_entityRefs);
  }

  public static function getRelatedEntities(
    $entityTableA, $entityIdA, $all = TRUE, $getCount = FALSE, $entityTableB = NULL,
    $groupBy = FALSE
  ) {
    $entityTableA = self::getEntityTable($entityTableA);
    if (empty($entityTableA) || empty($entityIdA)) {
      return;
    }

    $isActiveClause = '';
    if (!$all) {
      $isActiveClause = ' AND cre.is_active = 1 ';
    }
    $selectClause = '*';
    if ($getCount) {
      $selectClause = ' count(*) ';
    }

    $queryParams = [
      1 => [$entityTableA, 'String'],
      2 => [$entityIdA, 'Integer'],
    ];

    $entityTable1Clause = $entityTable2Clause = '';
    $entityTableB = self::getEntityTable($entityTableB);
    if ($entityTableB) {
      $entityTable1Clause = ' AND `entity_table_a`= %3 ';
      $entityTable2Clause = ' AND `entity_table_b`= %3 ';
      $queryParams[3] = [$entityTableB, 'String'];
    }

    $groupByClause = '';
    if ($groupBy) {
      $groupByClause = ' GROUP BY entity_table ';
    }

    $query = "
      SELECT {$selectClause} FROM (
        SELECT
          cre.`id`, cre.`entity_table_a` entity_table, cre.`entity_id_a` entity_id,
          crt.`label_b_a` relationship_name, cre.`is_active`
        FROM `civicrm_relate_entities` cre
        INNER JOIN civicrm_relationship_type crt
          ON crt.id = cre.relationship_type_id
        WHERE `entity_table_b`= %1 AND  `entity_id_b` = %2
          {$isActiveClause} $entityTable1Clause
        UNION

        SELECT
          cre.`id`, cre.`entity_table_b` entity_table, cre.`entity_id_b`,
          crt.`label_a_b` relationship_name, cre.`is_active`
        FROM `civicrm_relate_entities` cre
        INNER JOIN civicrm_relationship_type crt
          ON crt.id = cre.relationship_type_id
        WHERE `entity_table_a`= %1 AND `entity_id_a` = %2
          {$isActiveClause} $entityTable2Clause
      ) AS temp
      $groupByClause
    ";
    CRM_Core_DAO::disableFullGroupByMode();
    if ($getCount) {
      $result = CRM_Core_DAO::singleValueQuery($query, $queryParams);
    }
    else {
      $result = CRM_Core_DAO::executeQuery($query, $queryParams);
    }
    CRM_Core_DAO::reenableFullGroupByMode();
    return $result;
  }

}
