# Kohana-Cron

This module provides a way to schedule tasks (jobs) within your Kohana application. Based on the job done by [Chris Bandy][https://github.com/cbandy/kohana-cron]

Uses vendor [mtdowling/cron-expression][https://github.com/mtdowling/cron-expression].


## Installation

Step 1: Download the module into your modules subdirectory.

Step 2: Create table crontab

    CREATE TABLE IF NOT EXISTS  `crontab` (
      `id_crontab` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(50) NOT NULL,
      `period` varchar(50) NOT NULL,
      `callback` varchar(140) NOT NULL,
      `params` varchar(255) DEFAULT NULL,
      `description` varchar(255) DEFAULT NULL,
      `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `date_started` datetime  DEFAULT NULL,
      `date_finished` datetime  DEFAULT NULL,
      `date_next` datetime  DEFAULT NULL,
      `times_executed`  bigint DEFAULT NULL,
      `output` varchar(50) DEFAULT NULL,
      `running` tinyint(1) NOT NULL DEFAULT '0',
      `active` tinyint(1) NOT NULL DEFAULT '1',
      PRIMARY KEY (`id_crontab`),
      UNIQUE KEY `crontab_UK_name` (`name`)
    ) ENGINE=MyISAM DEFAULT;

Step 3: Enable the module in your bootstrap file:

	/**
	 * Enable modules. Modules are referenced by a relative or absolute path.
	 */
	Kohana::modules(array(
		'cron'       => MODPATH.'cron',
		// 'auth'       => MODPATH.'auth',       // Basic authentication
		// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
		// 'database'   => MODPATH.'database',   // Database access
		// 'image'      => MODPATH.'image',      // Image manipulation
		// 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
		// 'pagination' => MODPATH.'pagination', // Paging of results
		// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	));

## Usage

In its simplest form, a task is a [PHP callback][1] and times at which it should run.
To configure a task call `Cron::set($name, array($frequency, $callback))` where
`$frequency` is a string of date and time fields identical to those found in [crontab][2].
For example,

	Cron::set('reindex_catalog', array('@daily', 'Catalog::regenerate_index'));
	Cron::set('calendar_notifications', array('*/5 * * * *', 'Calendar::send_emails'));

Configured tasks are run with their appropriate frequency by calling `Cron::run()`. Call
this method in your bootstrap file, and you're done!


## Advanced Usage

A task can also be an instance of `Cron` that extends `next()` and/or `execute()` as
needed. Such a task is configured by calling `Cron::set($name, $instance)`.

If you have access to the system crontab, you can run Cron less (or more) than once
every request. You will need to modify the lines where the request is handled in your
bootstrap file to prevent extraneous output. The default is:

	
	echo Request::factory()
            ->execute()
            ->send_headers()
            ->body();

Change it to:

	if ( ! defined('SUPPRESS_REQUEST'))
    {
        echo Request::factory()
            ->execute()
            ->send_headers()
            ->body();
    }

Then set up a system cron job to run your application's Cron once a minute:

* * * * * /usr/bin/php -f /var/www/open-classifieds/oc/modules/common/modules/cron/cron.php

The included `run.php` should work for most cases, but you are free to call `Cron::run()`
in any way you see fit.


  [1]: http://php.net/manual/language.pseudo-types.php#language.types.callback
  [2]: http://linux.die.net/man/5/crontab
