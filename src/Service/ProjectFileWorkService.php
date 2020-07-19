<?php


namespace App\Service;


use App\Entity\File;
use App\Entity\ProjectInfo;
use App\Entity\ProjectStatistics;
use Doctrine\ORM\EntityManager;

class ProjectFileWorkService
{
    public static function isFilePathValid(ProjectInfo $projectInfo , string $fileName, ?string $path=null) :bool
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

    public static function getFilePath(ProjectInfo $projectInfo, string $fileName, ?string $path = null)
    {
        $path = str_replace('src/Service', "Users/" . $projectInfo->getUser()->getNickname() . "/" . $projectInfo->getProjectName() . "/" . $path . $fileName, __DIR__);
        return $path;
    }

    public static function flushFileDataToDB(EntityManager $entityManager, ProjectInfo $project, ?string $path, $file, $fileName)
    {
        $fileEntity = $entityManager->getRepository(File::class)->findOneBy(['path' => $path]);
        $fileEntity->setFile($file);

        $entityManager->persist($fileEntity);
        $entityManager->flush();

        $projectStatistic = new ProjectStatistics();
        $projectStatistic->setFile($fileEntity);
        $projectStatistic->setCreatedAt(new \DateTime('now'));
        $projectStatistic->setChanging($file);
        $projectStatistic->setFilePath($path);
        $projectStatistic->setProjectInfo($project);
        $projectStatistic->setIp($_SERVER['REMOTE_ADDR']);

        $entityManager->persist($projectStatistic);
        $entityManager->flush();

        $file_array =  file($path);
        $oldNum =  count($file_array);
        $newNum = substr_count($file, "\n") + 1;
        $project->setUpdatedAt(new \DateTime());
        $project->setCountOfLines($project->getCountOfLines()- $oldNum + $newNum);

        $entityManager->persist($project);
        $entityManager->flush();
    }
}