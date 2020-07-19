<?php


namespace App\Controller;


use App\Entity\File;
use App\Entity\ProjectInfo;
use App\Entity\ProjectStatistics;
use App\Entity\User;
use App\Form\EditFileFormType;
use App\Service\ProjectFileWorkService;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectFileWorkController extends AbstractController
{
    /**
     * @Route(path="/project/{id}/{projectName}/{fileName}", methods={"GET", "POST"}, requirements={"fileName"="[a-zA-Z0-9_.()]+\.[a-z]+"})
     * @param Request $request
     * @param int $id
     * @param string $projectName
     * @param string $fileName
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function ProjectFile(Request $request, int $id, string $projectName, string $fileName)
    {
        preg_match('/(?<=\.)[a-z]+$/', $fileName, $extension);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $log = UserDataService::isLogged($user);
        $em = $this->getDoctrine()->getManager();
        $currentUser = $em->getRepository(User::class)->find($id);
        $project = $em->getRepository(ProjectInfo::class)->findOneBy([
            'projectName' => $projectName,
            'user' => $currentUser,
        ]);

        if(!$project || $user !== $currentUser && !$project->getIsOpen()) {
            return $this->redirect('/user/' . $id);
        }

        if(!$isFilePathValid = ProjectFileWorkService::isFilePathValid($project, $fileName)) {
            return $this->redirect('/project/' . $id . '/' . $projectName);
        }

        if ($user === $currentUser) {
            $legalToChange = true;
        } else {
            $legalToChange = false;
        }

        $file = fopen($path = ProjectFileWorkService::getFilePath($project, $fileName), 'r+');
        $content = '';
        while (!feof($file)) {
            $content .= fgets($file);
        }

        $formData = [
            'file' => $content,
            'fileName' => $fileName,
            'legalToChange' => $legalToChange,
        ];

        $fileForm = $this->createForm(EditFileFormType::class, $formData);

        $fileForm->handleRequest($request);

        if ($fileForm->isSubmitted()) {
            $data = $fileForm->getData();

            /**
             * @todo Refactor this (push to service)
             */

            $fileEntity = $em->getRepository(File::class)->findOneBy(['path' => $path]);
            $fileEntity->setFile($data['file']);

            $em->persist($fileEntity);
            $em->flush();

            $projectStatistic = new ProjectStatistics();
            $projectStatistic->setFile($fileEntity);
            $projectStatistic->setCreatedAt(new \DateTime('now'));
            $projectStatistic->setChanging($data['file']);
            $projectStatistic->setFilePath($path);
            $projectStatistic->setProjectInfo($project);
            $projectStatistic->setIp($_SERVER['REMOTE_ADDR']);

            $em->persist($projectStatistic);
            $em->flush();

            $file_array =  file (ProjectFileWorkService::getFilePath($project, $fileName));
            $oldNum =  count($file_array);
            $newNum = substr_count($data['file'], "\n") + 1;
            $project->setUpdatedAt(new \DateTime());
            $project->setCountOfLines($project->getCountOfLines()- $oldNum + $newNum);

            $em->persist($project);
            $em->flush();

            file_put_contents($path, $data['file']);

            return $this->redirect('/project/' . $id . '/' . $projectName);
        }

        return $this->render('file/f_edit.html.twig', [
            'log' => $log,
            'user' => $user,
            'fileForm' => $fileForm->createView(),
            'legalToChange' => $legalToChange,
            'extension' => $extension[0],
        ]);
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