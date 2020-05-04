<?php

/**
 * This class generates form components for Bank Account
 */
class CRM_RelateEntities_Form_RelatedEntity extends CRM_Core_Form {
use CRM_Core_Form_EntityFormTrait;

  public $_id;

  /**
   * Set variables up before form is built.
   */
  public function preProcess() {
    parent::preProcess();
    $this->entity_table = CRM_Utils_Request::retrieve('entityTable', 'String', $this, TRUE);
    $this->_id = CRM_Utils_Request::retrieve('id', 'String', $this);
    if (!$this->_id) {
      $this->entity_id = CRM_Utils_Request::retrieve('entityId', 'Positive', $this, TRUE);
    }

    $this->entityA = ($this->entity_table == 'Contact') ? 'Contact' : 'FinancialType';
    $this->entityB = ($this->entity_table == 'FinancialType') ? 'Contact' : 'FinancialType';
  }

  /**
   * Set entity fields to be assigned to the form.
   */
  protected function setEntityFields() {
    $this->entityFields = [
      'is_active' => [
        'name' => 'is_active',
        'required' => TRUE,
      ],
    ];
    return $this->entityFields;
  }

  /**
   * Classes extending CRM_Core_Form should implement this method.
   *
   */
  public function getDefaultContext() {
    return 'create';
  }

  /**
   * Classes extending CRM_Core_Form should implement this method.
   */
  public function getDefaultEntity() {
    return 'RelateEntities';
  }

  /**
   * Set the delete message.
   *
   * We do this from the constructor in order to do a translation.
   */
  public function setDeleteMessage() {
    $this->deleteMessage = ts('Deleting a related entity cannot be undone.') . ts('Do you want to continue?');
  }

  /**
   * Add defined entity field to template.
   */
  protected function addEntityFieldsToTemplates() {
    foreach ($this->setEntityFields() as $fieldSpec) {
      if (empty($fieldSpec['not-auto-addable'])) {
        $element = $this->addField($fieldSpec['name'], CRM_Utils_Array::value('props', $fieldSpec, []), CRM_Utils_Array::value('required', $fieldSpec));
        if (!empty($fieldSpec['is_freeze'])) {
          $element->freeze();
        }
      }
    }
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    self::buildQuickEntityForm();
    if ($this->_action & CRM_Core_Action::DELETE) {
      $this->addButtons([
        [
          'type' => 'next',
          'name' => ts('Delete'),
          'isDefault' => TRUE,
        ],
        [
          'type' => 'cancel',
          'name' => ts('Cancel'),
        ],
      ]);
      return;
    }


    $this->addEntityFieldsToTemplates();
    $this->addEntityRef(
      'entity_a',
      ts('Entity A'),
      [
        'entity' => $this->entityA,
      ],
      TRUE
    )->freeze();

    $this->addEntityRef(
      'entity_b',
      ts('Entity B'),
      [
        'entity' => $this->entityB,
        'select' => ['minimumInputLength' => 0],
      ],
      TRUE
    );

    $this->add(
      'select',
      'relationship_type_id',
      ts('Relationship Type'),
      CRM_Contact_BAO_Relationship::getContactRelationshipType(NULL, NULL, NULL, NULL, TRUE),
      FALSE,
      ['class' => 'crm-select2', 'placeholder' => ts('Select Relationship Types')]
    );

    $fields = [
      'entity_a' => [
        'name' => 'entity_a',
      ],
      'relationship_type_id' => [
        'name' => 'relationship_type_id',
      ],
      'entity_b' => [
        'name' => 'entity_b',
      ]
    ] + $this->entityFields;
    $this->assign('entityFields', $fields);
    $this->addButtons([
      [
        'type' => 'next',
        'name' => ts('Save'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'next',
        'name' => ts('Save and New'),
        'subName' => 'new',
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ],
    ]);
    $this->addFormRule(array('CRM_RelateEntities_Form_RelatedEntity', 'formRule'), $this);
  }

  /**
   * Global form rule.
   *
   * @param array $fields
   *   The input form values.
   * @param array $files
   *   The uploaded files if any.
   * @param $self
   *
   * @return bool|array
   *   true if no errors, else array of errors
   */
  public static function formRule($fields, $files, $self) {
    $error = [];
    return $error;
  }

  /**
   * Set the default values for the form.
   */
  public function setDefaultValues() {
    $defaults = [];
    if (!$this->_id) {
      $defaults['is_active'] = 1;
      $defaults['entity_a'] = $this->entity_id;
    }
    else {
      $defaults = civicrm_api3('RelateEntities', 'getsingle', [
        'id' => $this->_id,
      ]);
      if ($defaults['entity_table_b'] == CRM_RelateEntities_Utils::getEntityTable($this->entityB)) {
        $defaults['entity_b'] = $defaults['entity_id_b'];
        $defaults['relationship_type_id'] .= '_a_b';
      }
      else if ($defaults['entity_table_a'] == CRM_RelateEntities_Utils::getEntityTable($this->entityB)) {
        $defaults['entity_b'] = $defaults['entity_id_a'];
        $defaults['relationship_type_id'] .= '_b_a';
      }
      if ($defaults['entity_table_b'] == CRM_RelateEntities_Utils::getEntityTable($this->entityA)) {
        $defaults['entity_a'] = $defaults['entity_id_b'];
      }
      else if ($defaults['entity_table_a'] == CRM_RelateEntities_Utils::getEntityTable($this->entityA)) {
        $defaults['entity_a'] = $defaults['entity_id_a'];
      }
    }
    return $defaults;
  }

  /**
   * Process the form submission.
   */
  public function postProcess() {
    if ($this->_action & CRM_Core_Action::DELETE) {
      $result = civicrm_api3('RelateEntities', 'delete', [
        'id' => $this->_id,
      ]);
      if (!empty($result['is_error'])) {
        CRM_Core_Error::statusBounce($result['error_message'], CRM_Utils_System::url('civicrm/', "reset=1&action=browse"), ts('Cannot Delete'));
      }
      CRM_Core_Session::setStatus(ts('Selected Related entity has been deleted.'), ts('Record Deleted'), 'success');
    }
    else {
      // store the submitted values in an array
      $params = $this->_submitValues;
      try {
        $apiParams = $this->buildParams($params);
        if ($this->_id) {
          $apiParams['id'] = $this->_id;
        }
        civicrm_api3('RelateEntities', 'create', $apiParams);
        CRM_Core_Session::setStatus(ts('The Related entity has been saved.'), ts('Saved'), 'success');
      }
      catch (CRM_Core_Exception $e) {
        CRM_Core_Error::statusBounce($e->getMessage());
      }
    }

    $buttonName = $this->controller->getButtonName();
    $session = CRM_Core_Session::singleton();
    if ($this->entity_table == 'Contact') {
      $this->ajaxResponse['updateTabs']['#tab_relateentities'] = CRM_RelateEntities_Utils::getRelatedEntities(
        'Contact', $apiParams['entity_id_a'], FALSE, TRUE
      );
    }
    if ($buttonName == $this->getButtonName('next', 'new')) {
      CRM_Core_Session::setStatus(ts(' You can add another Related entity.'));
      $session->replaceUserContext(CRM_Utils_System::url('civicrm/relatedentities/add',
        "action=add&reset=1&entityTable={$this->entity_table}&entityId={$apiParams['entity_id_a']}")
      );
    }
    else {
      if ($this->entity_table == 'Contact') {
        $url = CRM_Utils_System::url('civicrm/contact/view',
          "reset=1&force=1&cid={$apiParams['entity_id_a']}&selectedChild=relateentities");
      }
      if ($this->entity_table == 'FinancialType') {
        $url = CRM_Utils_System::url('civicrm/admin/financial/financialType', 'reset=1');
      }
      $session->replaceUserContext($url);
    }
  }

  public function buildParams($formValues) {
    $rType = explode('_', $formValues['relationship_type_id'], 2);
    if ($rType[1] == 'a_b') {
      $params = [
        'entity_id_a' => $formValues['entity_a'],
        'entity_id_b' => $formValues['entity_b'],
        'entity_table_a' => CRM_RelateEntities_Utils::getEntityTable($this->entityA),
        'entity_table_b' => CRM_RelateEntities_Utils::getEntityTable($this->entityB),
      ];
    }
    else {
      $params = [
        'entity_id_a' => $formValues['entity_b'],
        'entity_id_b' => $formValues['entity_a'],
        'entity_table_a' => CRM_RelateEntities_Utils::getEntityTable($this->entityB),
        'entity_table_b' => CRM_RelateEntities_Utils::getEntityTable($this->entityA),
      ];
    }
    $params['relationship_type_id'] = $rType[0];
    $params['is_active'] = $formValues['is_active'];
    return $params;
  }

}
