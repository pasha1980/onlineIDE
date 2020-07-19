<?php


namespace App\Service;


use App\Entity\ProjectInfo;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Types\Resource_;

class ProjectFileWorkService
{
    public static function isFilePathValid(ProjectInfo $projectInfo , string $fileName, string $path=null) :bool
    {
        $filePath = $projectInfo->getFilenames();
        $allPath = ProjectFileWorkService::getFilePath($projectInfo, $fileName, $path);

        foreach ($filePath as $value) {
            if($value === $allPath) {
                $response = true;
                break;
            } else {
                $response = false;
            }
        }
        return $response;
    }

    public static function getFilePath(ProjectInfo $projectInfo, string $fileName, string $path = null)
    {
        $path = str_replace('src/Service', "Users/" . $projectInfo->getUser()->getNickname() . "/" . $projectInfo->getProjectName() . "/" . $path . $fileName, __DIR__);
        return $path;
    }

//    public static function flushFileDataToDB(EntityManager $entityManager, ProjectInfo $project, )
}