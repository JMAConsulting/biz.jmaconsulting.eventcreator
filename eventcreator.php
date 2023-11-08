<?php
  define('CREATED_ID', 830);
  define('EDITED_ID', 831);

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
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function eventcreator_civicrm_install() {
  _eventcreator_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function eventcreator_civicrm_enable() {
  _eventcreator_civix_civicrm_enable();
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
      'custom_' . CREATED_ID => CRM_Core_Session::singleton()->getLoggedInContactID(),
    ]);
  }
  if ($objectName == 'Event' && $op == 'edit') {
    civicrm_api3('CustomValue', 'create', [
      'entity_id' => $objectId,
      'custom_' . EDITED_ID => CRM_Core_Session::singleton()->getLoggedInContactID(),
    ]);
  }
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *

 // */

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
