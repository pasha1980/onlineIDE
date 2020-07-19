<?php


namespace App\Controller;


use App\Entity\ProjectInfo;
use App\Entity\User;
use App\Form\EditFileFormType;
use App\Service\ProjectFileWorkService;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->ProjectPathFile($request, $id, $projectName, null, $fileName);
    }

    /**
     * @Route(path="/project/{id}/{projectName}/{path}/{fileName}", methods={"GET", "POST"}, requirements={"path" = ".+(?=(\/[a-zA-Z0-9_.()]+\.[a-z]+)|\/)", "fileName" = "[a-zA-Z0-9_.()]+\.[a-z]+"})
     * @param Request $request
     * @param int $id
     * @param string $projectName
     * @param string $path
     * @param string $fileName
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ProjectPathFile(Request $request, int $id, string $projectName, ?string $path, string $fileName)
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

        if($path) {
            $path .= '/';
        }

        if(!$project || $user !== $currentUser && !$project->getIsOpen()) {
            return $this->redirect('/user/' . $id);
        }

        if(!$isFilePathValid = ProjectFileWorkService::isFilePathValid($project, $fileName, $path)) {
            return $this->redirect('/project/' . $id . '/' . $projectName);
        }

        if ($user === $currentUser) {
            $legalToChange = true;
        } else {
            $legalToChange = false;
        }

        $file = fopen($dirPath = ProjectFileWorkService::getFilePath($project, $fileName, $path), 'r+');
        $content = '';
        while (!feof($file)) {
            $content .= fgets($file);
        }
        fclose($file);

        $formData = [
            'file' => $content,
            'fileName' => $fileName,
            'legalToChange' => $legalToChange,
        ];

        $fileForm = $this->createForm(EditFileFormType::class, $formData);

        $fileForm->handleRequest($request);

        if ($fileForm->isSubmitted()) {
            $data = $fileForm->getData();
            ProjectFileWorkService::flushFileDataToDB($em, $project, $dirPath, $data['file'], $fileName);
            file_put_contents($dirPath, $data['file']);
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
}