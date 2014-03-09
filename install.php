<?php

/*
 * email Inactive Users
 *
 * @package eiu mod
 * @version 1.0
 * @author Jessica Gonz�lez <suki@missallsunday.com>
 * @copyright Copyright (c) 2014 Jessica Gonz�lez
 * @license http://www.mozilla.org/MPL/2.0/
 */


if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $context;

eiu_check();

db_extend('packages');

if (empty($context['uninstalling']))
{
		$smcFunc['db_add_column'](
			'{db_prefix}members',
			array(
				'name' => 'inactive_mail',
				'type' => 'int',
				'size' => 10,
				'null' => false,
			),
			array(),
			'update',
			null
		);

	// Create the scheduled task
	$smcFunc['db_insert'](
		'insert',
		'{db_prefix}scheduled_tasks',
		array(
			'id_task' => 'int',
			'next_time' => 'int',
			'time_offset' => 'int',
			'time_regularity' => 'int',
			'time_unit' => 'string',
			'disabled' => 'int',
			'task' => 'string',
		),
		array(
			0, 0, 0, 1, 'd', 0, 'emailInactiveUsers',
		),
		array(
			'id_task',
		)
	);
}

// Uninstalling?
else
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}scheduled_tasks
		WHERE task = {string:name}',
		array(
			'name' => 'emailInactiveUsers',
		)
	);

function eiu_check()
{
	if (version_compare(PHP_VERSION, '5.3.0', '<'))
		fatal_error('This mod needs PHP 5.3 or greater. You will not be able to install/use this mod, contact your host and ask for a php upgrade.');
}
