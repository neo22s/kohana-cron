<?php

/**
 * @package Cron
 *
 * @author      Chris Bandy
 * @copyright   (c) 2010 Chris Bandy
 * @license     http://www.opensource.org/licenses/isc-license.txt
 */

// Path to Kohana's index.php
$system = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'index.php';

if (file_exists($system))
{
	defined('SUPPRESS_REQUEST') or define('SUPPRESS_REQUEST', TRUE);

	include $system;

    //get all the cron jobs
    //Cron::set('email_test', array('*/5 * * * *', 'Controller_Home::email'));
    //Cron::set('log_test', array('*/3 * * * *', 'Controller_Home::log'));

    //execute them
	Cron::run();
}
