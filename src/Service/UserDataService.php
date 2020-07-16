<?php


namespace App\Service;



use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

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

    public static function getOnlyAnotherUsers($user, array $userList) :array
    {
        foreach ($userList as $key => $value)
        {
            $role = $value->getRoles();
            if ($role[0]=="ROLE_ADMIN") {
                unset($userList[$key]);
            }
        }

        unset($userList[array_search($user,$userList)]);

        return $userList;
    }

    public static function getAnotherUsersInfo(array $userList) :array
    {
        $info = [];
        $i = 0;
        foreach ($userList as $value)
        {
            if (!$value->getCompany()) {
                $value->setCompany('No data');
            }

            if (!$value->getDescription()) {
                $value->setDescription('No data');
            }

            $info[$i] = [
                'user' => $value,
                'number' => $i+1,
            ];
            $i++;
        }
        return $info;
    }
}