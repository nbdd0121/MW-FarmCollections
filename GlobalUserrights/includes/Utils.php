<?php

namespace FarmColle\GUR;

class Utils {

	public static function getGlobalGroups( \User $user ) {

		$uid = $user->getId();

		if ( $uid === 0 ) {
			return [];
		}

		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
				'global_user_groups',
				[ 'gug_group' ],
				[ 'gug_user' => $uid ],
				__METHOD__
				);

		$groups = [];

		foreach ( $res as $row ) {
			$groups[] = $row->gug_group;
		}

		return $groups;

	}
}
