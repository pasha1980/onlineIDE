<?php


namespace App\Service;


use App\Entity\File;
use App\Entity\ProjectInfo;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProjectNavigateService
{
    public static function CreateFolder(EntityManager $entityManager, string $folder, string $path, ProjectInfo $projectInfo) :?ProjectInfo
    {
        $folder = str_replace(' ', '_', $folder);
        $dirPath = str_replace('src/Service', 'Users/' . $projectInfo->getUser()->getNickname() . '/' . $projectInfo->getProjectName() . '/' . $path, __DIR__);

        $projectInfo->setUpdatedAt(new \DateTime('now'));
        $folders = $projectInfo->getFolders();

        $countOfSlashes = substr_count($folder, '/');

        if($countOfSlashes) {
            preg_match_all('/[a-zA-Z0-9()_]+/', $folder, $exFolder);
            $exPath = '';
            for ($i = 0; $i < count($exFolder[0]); $i++) {
                if ($i === 0) {
                    $slash = '';
                } else {
                    $slash = '/';
                }
                array_push($folders, $dirPath . $exPath . $slash . $exFolder[0][$i]);
                $exPath = $exPath . $slash . $exFolder[0][$i];
            }
        } else {
            array_push($folders, $dirPath . $folder);
        }

        $projectInfo->setFolders($folders);
        $projectInfo->setCountOfFolders($projectInfo->getCountOfFolders()+1+$countOfSlashes);

        try {
            $entityManager->persist($projectInfo);
            $entityManager->flush();
        } catch (\Exception $e) {
            return null;
        }

        $createDir = mkdir($dirPath . $folder, 0777, true);
        if ($createDir) {
            return $projectInfo;
        } else {
            return null;
        }
    }

    public static function CreateFile(EntityManager $entityManager, string $file, string $path, ProjectInfo $projectInfo) :?ProjectInfo
    {
        $file = str_replace(' ', '_', $file);
        $filePath = str_replace('src/Service', 'Users/' . $projectInfo->getUser()->getNickname() . '/' . $projectInfo->getProjectName() . '/' . $path, __DIR__);
        $projectInfo->setCountOfFiles($projectInfo->getCountOfFiles()+1);
        $projectInfo->setUpdatedAt(new \DateTime('now'));
        $filenames = $projectInfo->getFilenames();

        $countOfSlashes = substr_count($file, '/');

        if($countOfSlashes) {
            $folders = $projectInfo->getFolders();
            preg_match_all('/[a-zA-Z0-9()_]+\//', $file, $exFolder);

            for ($i = 0; $i<count($exFolder[0]); $i++) {
                $exFolder[0][$i] = str_replace('/', '', $exFolder[0][$i]);
            }
            $exPath = '';
            for ($i = 0; $i < count($exFolder[0]); $i++) {
                if ($i === 0) {
                    $slash = '';
                } else {
                    $slash = '/';
                }
                array_push($folders, $filePath . $exPath . $slash . $exFolder[0][$i]);
                $exPath = $exPath . $slash . $exFolder[0][$i];
            }
            preg_match('/[a-zA-Z0-9()_]+\.[a-z]+/', $file, $exFile);
            $allPath = $filePath . $exPath . '/' . $exFile[0];
            try {
                $createDir = mkdir($filePath . $exPath, 0777, true);
            } catch (\Exception $e) {
                die('folder');
            }

            $projectInfo->setFolders($folders);

        } else {
            $allPath = $filePath . $file;
        }

        array_push($filenames, $allPath);

        $projectInfo->setFilenames($filenames);

        try {
            $entityManager->persist($projectInfo);
            $entityManager->flush();
        } catch (\Exception $e) {
            die('entity');
            return null;
        }


        $fileEntity = new File();
        $fileEntity->setProject($projectInfo);
        $fileEntity->setPath($allPath);

        $entityManager->persist($fileEntity);
        $entityManager->flush();



        try {
            $createFile = fopen($filePath . $file, 'w');
        } catch (\Exception $e) {
            die('file');
            return null;
        }

        if($createFile) {
            return $projectInfo;
        } else {
            die('if else');
            return null;
        }

        /**
         * @todo: when ending - change all "die" on exception
         */
    }

    public static function GetProjectInfoForWork(ProjectInfo $projectInfo, string $path) :array
    {
        $info = [
            'folder' => [],
            'file' => [],
        ];

        $folders = $projectInfo->getFolders();
        $files = $projectInfo->getFilenames();
        $currentPath = str_replace('src/Service', 'Users/' . $projectInfo->getUser()->getNickname() . '/' . $projectInfo->getProjectName() . '/' . $path, __DIR__);

        foreach ($folders as $value) {
            $directory = str_replace($currentPath, '', $value);
            if(strpos($directory, '/') === false) {
                array_push($info['folder'], $directory);
            }
        }

        foreach ($files as $value) {
            $directory = str_replace($currentPath, '', $value);
            if(strpos($directory, '/') === false ) {
                array_push($info['file'], $directory);
            }
        }

        return $info;
    }

    public static function isPathValid(ProjectInfo $projectInfo, string $path) :bool
    {
        $path = str_replace('src/Service', 'Users/' . $projectInfo->getUser()->getNickname() . '/' . $projectInfo->getProjectName(), __DIR__) . '/' . $path;
        $foldersList = $projectInfo->getFolders();
        foreach ($foldersList as $value) {
            if($path === $value) {
                $valid = true;
                break;
            } else {
                $valid = false;
            }
        }
        return $valid;
    }
}