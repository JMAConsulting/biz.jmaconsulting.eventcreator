<?php
  define('CREATED_ID', 830);
  define('EDITED_ID', 831)

require_once 'eventcreator.civix.php';
use CRM_Eventcreator_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */
function eventcreator_civicrm_config(&$config) {
  _eventcreator_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function eventcreator_civicrm_xmlMenu(&$files) {
  _eventcreator_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function eventcreator_civicrm_install() {
  _eventcreator_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function eventcreator_civicrm_postInstall() {
  _eventcreator_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function eventcreator_civicrm_uninstall() {
  _eventcreator_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function eventcreator_civicrm_enable() {
  _eventcreator_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function eventcreator_civicrm_disable() {
  _eventcreator_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function eventcreator_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventcreator_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function eventcreator_civicrm_managed(&$entities) {
  _eventcreator_civix_civicrm_managed($entities);
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
function eventcreator_civicrm_caseTypes(&$caseTypes) {
  _eventcreator_civix_civicrm_caseTypes($caseTypes);
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
function eventcreator_civicrm_angularModules(&$angularModules) {
  _eventcreator_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function eventcreator_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eventcreator_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function eventcreator_civicrm_entityTypes(&$entityTypes) {
  _eventcreator_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function eventcreator_civicrm_themes(&$themes) {
  _eventcreator_civix_civicrm_themes($themes);
}

function eventcreator_civicrm_pre($op, $objectName, $id, &$params) {
  if ($objectName == 'Event' && $op == 'edit') {
    if (!empty($params['is_active'])) {
      // Check if event was previously inactive.
      if (!empty($id)) {
        $isActive = civicrm_api3("Event", "getvalue", [
          "id" => $id,
          "return" => "is_active",
        ]);
        if (empty($isActive)) {
          $eventTitle = CRM_Core_DAO::singleValueQuery("SELECT title FROM civicrm_event WHERE id = %1", [1 => [$id, "Integer"]]);
          civicrm_api3('Activity', 'create', [
            'source_record_id' => $id,
            'source_contact_id' => "user_contact_id",
            'activity_type_id' => "Event Marked Active",
            'subject' => "Event $eventTitle ($id) Marked Active",
            'target_id' => "user_contact_id",
            'status_id' => "Completed",
          ]);
        }
      }
    }
  }
}

function eventcreator_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Event' && $op == 'create') {
    civicrm_api3('CustomValue', 'create', [
      'entity_id' => $objectId,
      'custom_' . CREATED_ID => CRM_Core_Session::singleton()->get('userID'),
    ]);
  }
  if ($objectName == 'Event' && $op == 'edit') {
    civicrm_api3('CustomValue', 'create', [
      'entity_id' => $objectId,
      'custom_' . EDITED_ID => CRM_Core_Session::singleton()->get('userID'),
    ]);
  }
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function eventcreator_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function eventcreator_civicrm_navigationMenu(&$menu) {
  _eventcreator_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _eventcreator_civix_navigationMenu($menu);
} // */
