<?php

class CRM_RelateEntities_Page_Tab extends CRM_Core_Page_Basic {
  /**
   * The action links that we need to display for the browse screen.
   *
   * @var array
   */
  public static $_links = NULL;

  /**
   * Get BAO Name.
   *
   * @return string
   *   Classname of BAO.
   */
  public function getBAOName() {
    return 'CRM_RelateEntities_DAO_RelateEntities';
  }

  /**
   * Get action Links.
   *
   * @return array
   *   (reference) of action links
   */
  public function &links() {
    if (!(self::$_links)) {
      self::$_links = [
        CRM_Core_Action::UPDATE => [
          'name' => ts('Edit'),
          'url' => 'civicrm/relateentities/list',
          'qs' => 'reset=1&action=update&id=%%id%%&entityTable=%%entityTable%%',
          'title' => ts('Edit'),
        ],
        CRM_Core_Action::DISABLE => [
          'name' => ts('Disable'),
          'ref' => 'crm-enable-disable',
          'title' => ts('Disable'),
        ],
        CRM_Core_Action::ENABLE => [
          'name' => ts('Enable'),
          'ref' => 'crm-enable-disable',
          'title' => ts('Enable'),
        ],
        CRM_Core_Action::DELETE => [
          'name' => ts('Delete'),
          'url' => 'civicrm/relateentities/list',
          'qs' => 'reset=1&action=delete&id=%%id%%',
          'title' => ts('Delete'),
        ],
      ];
    }
    return self::$_links;
  }

  /**
   * Browse all address formats for country.
   */
  public function browse() {
    $this->_id = CRM_Utils_Request::retrieve('entityId', 'Positive', $this, TRUE);
    $this->_entityTable = CRM_Utils_Request::retrieve('entityTable', 'String', $this, TRUE);
    $this->assign('contactId', $this->_id);
    $this->assign('contactID', $this->_id);

    // check logged in url permission
    if ($this->_entityTable == 'Contact') {
      $this->_contactId = $this->_id;
      CRM_Contact_Page_View::checkUserPermission($this);
    }

    CRM_RelateEntities_Utils::buildEntitiesList($this, $this->_entityTable, $this->_id);

    $this->ajaxResponse['tabCount'] = CRM_RelateEntities_Utils::getRelatedEntities(
      $this->_entityTable, $this->_id, FALSE, TRUE
    );
  }

  /**
   * Get name of edit form.
   *
   * @return string
   *   Classname of edit form.
   */
  public function editForm() {
    return 'CRM_RelateEntities_Form_RelatedEntity';
  }

  /**
   * Get edit form name.
   *
   * @return string
   *   name of this page.
   */
  public function editName() {
    return ts('Relate Entity');
  }

  /**
   * Get user context.
   *
   * @param null $mode
   *
   * @return string
   *   user context.
   */
  public function userContext($mode = NULL) {
    return 'civicrm/relateentities/list';
  }

}
