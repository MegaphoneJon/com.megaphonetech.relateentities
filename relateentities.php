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
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function relateentities_civicrm_install() {
  _relateentities_civix_civicrm_install();
  CRM_RelateEntities_Settings::install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function relateentities_civicrm_uninstall() {
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
