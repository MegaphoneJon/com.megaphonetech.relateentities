<?php

require_once 'relateentities.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function relateentities_civicrm_config(&$config) {
  _relateentities_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function relateentities_civicrm_xmlMenu(&$files) {
  _relateentities_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function relateentities_civicrm_install() {
  _relateentities_civix_civicrm_install();
  CRM_RelateEntities_Settings::install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function relateentities_civicrm_postInstall() {
  _relateentities_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function relateentities_civicrm_uninstall() {
  _relateentities_civix_civicrm_uninstall();
  CRM_RelateEntities_Settings::uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function relateentities_civicrm_enable() {
  _relateentities_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function relateentities_civicrm_disable() {
  _relateentities_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function relateentities_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _relateentities_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function relateentities_civicrm_managed(&$entities) {
  _relateentities_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function relateentities_civicrm_caseTypes(&$caseTypes) {
  _relateentities_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function relateentities_civicrm_angularModules(&$angularModules) {
  _relateentities_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function relateentities_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _relateentities_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function relateentities_civicrm_entityTypes(&$entityTypes) {
  _relateentities_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function relateentities_civicrm_themes(&$themes) {
  _relateentities_civix_civicrm_themes($themes);
}

/**
 * Implements hook_civicrm_tabset().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_tabset
 */
function relateentities_civicrm_tabset($tabsetName, &$tabs, $context) {
  if ('civicrm/contact/view' == $tabsetName) {
    $tabs[] = [
      'id' => 'relateentities',
      'url' =>  CRM_Utils_System::url(
        'civicrm/relateentities/list',
        "reset=1&entityId={$context['contact_id']}&entityTable=Contact"
      ),
      'title' => ts('Related Entities'),
      'weight' => '35',
      'count' => CRM_RelateEntities_Utils::getRelatedEntities(
        'Contact', $context['contact_id'], FALSE, TRUE
      ),
      'class' => 'livePage',
      'icon' => 'crm-i fa-handshake-o',
    ];
  }
}

function relateentities_civicrm_pageRun(&$page) {
  if (is_a($page, 'CRM_Financial_Page_FinancialType')) {
    $page->assign('entityTable', 'FinancialType');
    $financialTypes = $page->getTemplateVars('rows');
    foreach ($financialTypes as &$financialType) {
      $url = CRM_Utils_System::url(
        'civicrm/relateentities/list',
        "reset=1&entityTable=FinancialType&entityId={$financialType['id']}"
      );
      $financialType['expand'] = '<td><a class="nowrap bold crm-expand-row" title="' . ts('view related entity') . '" href="' . $url . '"></a></td>';
    }
    $page->assign('rows', $financialTypes);
    CRM_Core_Region::instance('page-body')->add([
      'template' => 'CRM/RelateEntities/Expand.tpl',
    ]);
  }
}
