<?php


namespace App\Service;


use App\Entity\ProjectInfo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProjectDataService
{
    public static function pushCreatedProjectToDB(EntityManagerInterface $entityManager, ProjectInfo $projectInfo, User $user, int $config) :bool
    {
        switch ($config) {
            case 1:
                $projectInfo->setCli(true);
                $projectInfo->setWebsite(false);
                $projectInfo->setLayout(false);
                break;
            case 2:
                $projectInfo->setCli(false);
                $projectInfo->setWebsite(true);
                $projectInfo->setLayout(false);
                break;
            case 3:
                $projectInfo->setCli(false);
                $projectInfo->setWebsite(false);
                $projectInfo->setLayout(true);
                break;
        }
        $name = $projectInfo->getProjectName();
        $newName = str_replace(' ', '_', $name);
        $projectInfo->setProjectName($newName);

        /**
         * @todo: release javascript: check if it is a space in name and replace ' ' with '_'
         */

        $projectInfo->setCreatedAt(new \DateTime('now'));
        $projectInfo->setUpdatedAt(new \DateTime('now'));
        $projectInfo->setUser($user);
        $projectInfo->setCountOfFiles(0);
        $projectInfo->setCountOfFolders(0);
        $projectInfo->setCountOfLines(0);
        try {
            $entityManager->persist($projectInfo);
            $entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    public static function createProjectFolder(ProjectInfo $projectInfo, User $user) :bool
    {
        $pName = $projectInfo->getProjectName();
        $uName = $user->getNickname();

        /**
         * @todo: Security with projectName
         */

        $path = str_replace('/src/Service', '/Users/' . $uName . '/', __DIR__);
        $createDir = mkdir($path . $pName, 0777, true);
        if($createDir) {
            return true;
        } else {
            return false;
        }

    }

    public static function getProjectInfoForListing(array $projectList) :array
    {
        $info = [];
        for ($i=0; $i < count($projectList); $i++)
        {
            if($projectList[$i]->getIsOpen()) {
                $openClose = "Open";
                $color = 'green';
            } else {
                $openClose = "Close";
                $color = 'red';
            }

            if($projectList[$i]->getCli()) {
                $type = 'Console project';
            } elseif ($projectList[$i]->getlayout()) {
                $type = 'Front-end project';
            } elseif ($projectList[$i]->getWebsite()) {
                $type = 'Website project';
            } else {
                $type = 'n/a';
            }

            $info[$i] = [
                'number' => ($i+1),
                'project' => $projectList[$i],
                'openclose' => $openClose,
                'type' => $type,
                'color' => $color,
            ];
        }
        return $info;
    }

}