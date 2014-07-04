<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Controller {


    public static function email()
    {
        Email::send('neo22s@gmail.com','chema',Date::unix2mysql(),Date::unix2mysql(),core::config('email.notify_email'), "no-reply ".core::config('general.site_name'));
    }

    public static function log()
    {
        Kohana::$log->add(Log::ERROR, Date::unix2mysql());
    }


} // End Welcome
