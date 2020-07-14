<?php


namespace App\Service;



use App\Entity\User;

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

    public static function verifyUser(User $user, $id) :bool
    {
        if ($user->getId() == $id) {
            return true;
        } else {
            return false;
        }
    }

}