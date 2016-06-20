<?php
namespace FarmColle\GUR;

class Hooks {

	public static function onUserEffectiveGroups( $user, &$groups ) {
		$groups = array_unique( array_merge( $groups, Utils::getGlobalGroups( $user ) ) );

		return true;
	}

	/**
	 * Hook function for SpecialListusersQueryInfo
	 * Updates UsersPager::getQueryInfo() to account for the global_user_groups table
	 * This ensures that global rights show up on Special:ListUsers
	 *
	 * @param $that instance of Use
	 * @param &$query the query array to be returned
	 */
	public static function onSpecialListusersQueryInfo( $that, &$query ) {
		// If not query by group, just skip
		if ( !isset( $query['conds']['ug_group'] ) ) {
			return true;
		}

		global $wgGlobalGroups;

		$group = $that->requestedGroup;

		// Not a global group, just skip
		if ( !in_array( $group, $wgGlobalGroups ) ) {
			return true;
		}

		// Unset the old query
		$query['tables'] = array_diff($query['tables'], ['user_groups']);
		unset( $query['join_conds']['user_groups'] );
		unset( $query['conds']['ug_group'] );

		// Set the new query
		$query['tables'][] = 'global_user_groups';
		$query['join_conds']['global_user_groups'] = [
			'LEFT JOIN',
			'user_id = gug_user'
		];
		$query['conds']['gug_group'] = $group;

		return true;
	}

	public static function onLoadExtensionSchemaUpdates( $updater ) {
		$dir = __DIR__ . '/sql';
		$updater->addExtensionTable( 'global_user_groups', "$dir/global_user_groups.sql" );
		return true;
	}
}
