<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectFileWorkController
{
    /**
     * @Route(path="/project/{id}/{projectName}/{fileName}", methods={"GET", "POST"}, requirements={"fileName"="[a-zA-Z0-9_.()]+\.[a-z]+"})
     * @param Request $request
     * @param int $id
     * @param string $projectName
     * @param string $fileName
     */
    public function ProjectFile(Request $request, int $id, string $projectName, string $fileName)
    {
        preg_match('/(?<=\.)[a-z]+$/', $fileName, $extension);
        dd($fileName, 'file', $extension[0]);

        /**
         * @todo: release working with file
         */
    }

    /**
     * @Route(path="/project/{id}/{projectName}/{path}/{fileName}", methods={"GET", "POST"}, requirements={"path" = ".+(?=(\/[a-zA-Z0-9_.()]+\.[a-z]+)|\/)", "fileName" = "[a-zA-Z0-9_.()]+\.[a-z]+"})
     * @param Request $request
     * @param int $id
     * @param string $projectName
     * @param string $path
     * @param string $fileName
     */
    public function ProjectPathFile(Request $request, int $id, string $projectName, string $path, string $fileName)
    {
        preg_match('/(?<=\.)[a-z]+$/', $fileName, $extension);
        dd($path = $path . '/', $fileName, 'path with file name', $extension[0]);
        /**
         * @todo: release working with path and file
         */
    }
}