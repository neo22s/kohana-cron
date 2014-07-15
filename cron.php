<?php

/**
 * Cron model
 *
 *
 * @package    OC
 * @category   Cron
 * @author     Chema <chema@open-classifieds.com>
 * @copyright  (c) 2009-2014 Open Classifieds Team
 * @license    GPL v3
 */

ignore_user_abort(true); 
set_time_limit(0);
ini_set('memory_limit', '1024M');

// Path to Kohana's index.php
$system = dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR.'index.php';

if (file_exists($system))
{
	defined('SUPPRESS_REQUEST') or define('SUPPRESS_REQUEST', TRUE);

	include $system;

    //execute all the crons
	echo Cron::run();
}
