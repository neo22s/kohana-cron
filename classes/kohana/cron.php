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

    /**
     * separator used in the table field params to separate the params we pass to the callback function.
     */
    const PARAMS_SEPARATOR = '|';
	
    /**
     * gets all the crons from crontab and executes them
     * @return string info
     */
	public static function run()
	{
        $time_start = microtime(true);

		//get active crons and due to execute now or next execute is NULL
        $crontab = new Cron();
        $crontab = $crontab->where('active','=',1)
                            ->where_open()
                            ->or_where('date_next','<=',Date::unix2mysql())
                            ->or_where('date_next','IS',NULL)
                            ->where_close()
                            ->find_all();

        $crons_executed = 0;
        foreach ($crontab as $cron) 
        {
            //check if cron is running, if running but passed treshold, lets launch it again...
            if ($cron->running == 0 OR ( $cron->running == 1 AND  ( time() - Date::mysql2unix($cron->date_started)) >= self::TRESHOLD ) )
            {
                $cron->execute();
                $crons_executed++;
            }
        }

        $seconds = microtime(true) - $time_start;
        
        return sprintf('Executed %d cronjobs in %d seconds',$crons_executed,$seconds);
	}



	/**
	 * Execute this cron job
     * @return bool/mixed false if fails, string with func output 
	 */
	public function execute()
	{
        $return = FALSE;

        if ($this->loaded())
        {
            //before launching the fucntion mark is as started and loading/running
            $this->date_started = Date::unix2mysql();
            $this->running      = 1;
            $this->save();

            //launch the function
            $return = $this->execute_call_back();

            //executed!
            if ($return!==FALSE)
            {
                //finished save finish , output and when will be executed next
                $this->date_finished = Date::unix2mysql();

                //when is next to be executed
                $this->date_next     = $this->get_next_date();

                //not running anymore
                $this->times_executed+=1;
                $this->running = 0;
                $this->output  = $return;

                $this->save();
            }    

        }

        return $return;
        
	}

    /**
     * executes the cron functions from the callback
     * @return function outup
     */
    public function execute_call_back()
    {
        $return = FALSE;

        if ($this->loaded() )
        {
            if (Cron::function_exists($this->callback))
            {
                $params = explode(self::PARAMS_SEPARATOR, $this->params);
                if (is_array($params))
                    $return = call_user_func_array($this->callback,$params);
                else
                    $return = call_user_func($this->callback);
            }
            else
            {
                //if function not found deactivate the cron
                $this->date_started = Date::unix2mysql();
                $this->active  = 0;
                $this->running = 0;
                $this->output  = 'Error: Function not found';
                $this->save();
            }
        }

        return $return;
    }

    /**
     * get next date the cron needs to be run
     * @return date mysql format
     */
    public function get_next_date()
    {
        if ($this->loaded())
        {
            //when is next? we used the started date as base
            require Kohana::find_file('vendor', 'autoload');//watch out for this in a futture may gave is troubles....
            $cron =  Cron\CronExpression::factory($this->period);
            return $cron->getNextRunDate($this->date_started)->format('Y-m-d H:i:s');
        }

        return NULL;
    }

    /**
     * checks if a call_back function name can be used
     * @param string $call_back function name
     * @return boolean
     */
    public static function function_exists($call_back)
    {
        if (function_exists($call_back))
        {
            return TRUE;
        }
        
        if (strpos($call_back, '::'))
        {
            $m=explode('::',$call_back);
            if (method_exists($m[0], $m[1]))
            {
                return TRUE;
            }
        } 
       
       return FALSE;
    
    }



}
