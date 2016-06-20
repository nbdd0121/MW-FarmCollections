# FarmCollections/GlobalUserrights 0.0.1
Providing global user groups & its management for wiki farms

## Motivation
There are other extensions achieving similar things already. But none of them are perfect:
* CentralAuth: Too heavy, and is created majorly for use of Wikimedia Foundation, difficult to configure and hard to maintain.
* GlobalUserGroups: This extension replicate global user rights changes to every wiki's local database, which introduces consistency issues and is poorly performed.
* GlobalUserrights: Shares the same name as this one. Its approach is similar to this one, but it cannot distinguish between local and global groups when editing user rights.

## Install
* [Install FarmCollections](../README.md)
* Add `wfLoadExtension('FarmCollections/GlobalUserrights');` to your LocalSettings.php
* Run the [update script](https://www.mediawiki.org/wiki/Manual:Update.php)
* You are done!

## Configuration
* `$wgGlobalGroups` (array of string): list of groups names that should be global
	* The only difference between global groups and local groups is their appearance in $wgGlobalGroups. So the normal way you configure groups just works.
* You can set user rights:
	* globaluserrights: the right needed to change global group membership of a user. It is recommended to only grant this right to users on the central (or meta) wiki, as the logs can not be shared across wikis.


