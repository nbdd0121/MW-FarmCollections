<?php
namespace FarmColle\GUR;

class UserrightsPage extends \UserrightsPage {

	public function __construct() {
		parent::__construct();
	}

	function changeableGroups() {
		$groups = parent::changeableGroups();

		global $wgGlobalGroups;

		// Disable global groups operations
		// In fact, remove groups from getAllGroups should be enough
		// but this method acts as an extra safeguard
		$groups['add'] = array_diff( $groups['add'], $wgGlobalGroups );
		$groups['remove'] = array_diff( $groups['remove'], $wgGlobalGroups );
		$groups['add-self'] = array_diff( $groups['add-self'], $wgGlobalGroups );
		$groups['remove-self'] = array_diff( $groups['remove-self'], $wgGlobalGroups );

		return $groups;
	}

	protected static function getAllGroups() {
		$groups = parent::getAllGroups();

		global $wgGlobalGroups;

		// Remove global groups from the list.
		$groups = array_diff( $groups, $wgGlobalGroups );

		return $groups;
	}
}
