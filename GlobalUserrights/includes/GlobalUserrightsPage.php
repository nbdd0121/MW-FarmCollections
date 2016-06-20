<?php
namespace FarmColle\GUR;

class FakeUser {

	private $user;

	public function __construct( $user ) {
		$this->user = $user;
	}

	public function getUser() {
		return $this->user;
	}

	public function getGroups() {
		return Utils::getGlobalGroups( $this->user );
	}

	// Delegate all other calls to $user
	public function __call( $method, $args ) {
		return call_user_func_array( [$this->user, $method], $args );
	}
}

class GlobalUserrightsPage extends \UserrightsPage {

	/* Constructor */
	public function __construct() {
		\SpecialPage::__construct( 'GlobalUserrights' );
	}

	function doSaveUserGroups( $user, $add, $remove, $reason = '' ) {
		$oldGroups = Utils::getGlobalGroups( $user->getUser() );
		$newGroups = $oldGroups;

		$uid = $user->getId();

		// remove groups
		foreach ( $remove as $group ) {
			$this->removeGroup( $uid, $group );
		}

		// add groups
		foreach ( $add as $group ) {
			$this->addGroup( $uid, $group );
		}

		$newGroups = array_diff( $newGroups, $remove );
		$newGroups = array_merge( $newGroups, $add );
		$newGroups = array_unique( $newGroups );

		// Ensure that caches are cleared
		$user->invalidateCache();

		// if anything changed, log it
		if ( $newGroups != $oldGroups ) {
			$this->addLogEntry( $user, $oldGroups, $newGroups, $reason );
		}
		return array( $add, $remove );
	}

	function addGroup( $uid, $group ) {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->insert(
				'global_user_groups',
				[
				'gug_user' => $uid,
				'gug_group' => $group
				],
				__METHOD__,
				'IGNORE'
				);
	}

	function removeGroup( $uid, $group ) {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete(
				'global_user_groups',
				[
				'gug_user' => $uid,
				'gug_group' => $group
				],
				__METHOD__
				);
	}

	function addLogEntry( $user, $oldGroups, $newGroups, $reason ) {
		$logEntry = new \ManualLogEntry( 'gblrights', 'rights' );
		$logEntry->setPerformer( $this->getUser() );
		$logEntry->setTarget( $user->getUserPage() );
		$logEntry->setParameters( [
				'4::oldgroups' => $oldGroups,
				'5::newgroups' => $newGroups,
		] );
		$logid = $logEntry->insert();
		$logEntry->publish( $logid );
	}

	public function fetchUser( $username ) {
		$status = parent::fetchUser( $username );

		if ( $status->isOK() ) {
			$user = $status->getValue();
			return \Status::newGood( new FakeUser( $user ) );
		}

		return $status;
	}

	function changeableGroups() {
		global $wgUser;
		global $wgGlobalGroups;

		// Users can change all global groups if he/she has the right `globaluserrights`
		// and none of them w/o the right, even if he/she has the right `userrights` locally
		if ( $wgUser->isAllowed( 'globaluserrights' ) ) {
			return [
				'add' => $wgGlobalGroups,
				'remove' => $wgGlobalGroups,
				'add-self' => [],
				'remove-self' => []
			];
		} else {
			return [];
		}
	}

	protected function showLogFragment( $user, $output ) {
		$log = new \LogPage( 'gblrights' );
		$output->addHTML( \Xml::element( 'h2', null, $log->getName() . "\n" ) );
		\LogEventsList::showLogExtract( $output, 'gblrights', $user->getUserPage()->getPrefixedText() );
	}

	protected static function getAllGroups() {
		global $wgGlobalGroups;

		return $wgGlobalGroups;
	}

}
