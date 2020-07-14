<?php


namespace App\Service;



class UserDataService
{
    public static function isLogged($user) :bool
    {
        $log = false;
        if ($user != 'anon.')
        {
            $log = true;
        }
        return $log;
    }

}