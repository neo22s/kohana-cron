<?php defined('SYSPATH') or die('No direct script access.');
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

class Kohana_Cron extends ORM {


    /**
     * Table name to use for the cron, by default crontab
     *
     * @access  protected
     * @var     string  $_table_name default [singular model name]
     */
    protected $_table_name = 'crontab';

    /**
     * Column to use as primary key
     *
     * @access  protected
     * @var     string  $_primary_key default [id]
     */
    protected $_primary_key = 'id_crontab';

    /**
     * Seconds whn we launch the cron again if didnt finish, this should be the maximum any of your crons takes to be executed.
     */
    const TRESHOLD         = 360;

	
	public static function run()
	{
        require Kohana::find_file('vendor', 'autoload');

        // Works with complex expressions
$cron =     Cron\CronExpression::factory('*/13 15 * * *');
 d($cron->getNextRunDate('2010-01-12 00:00:00')->format('Y-m-d H:i:s'));

		//get active crons and due to execute now or next execue is NULL
        //check if cron is running, if running but passed treshold, lets launch it again...
        //execute the cron
	}



	/**
	 * Execute this job
	 */
	public function execute()
	{
        //if loaded, save when starts mark it as running
        //launch the function
		call_user_func($this->callback);
        //finished save finish and output and when will be executed next
        

        
	}




}
