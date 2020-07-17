<?php


namespace App\Service;


use App\Entity\ProjectInfo;
use Doctrine\ORM\EntityManager;

class ProjectWorkService
{
    public static function CreateFolder(EntityManager $entityManager, string $folder, string $path, ProjectInfo $projectInfo) :?ProjectInfo
    {


        return null;
    }

    public static function CreateFile(EntityManager $entityManager, string $file, string $path, ProjectInfo $projectInfo) :?ProjectInfo
    {


        return null;
    }
}