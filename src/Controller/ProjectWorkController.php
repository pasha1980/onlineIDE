<?php


namespace App\Controller;


use App\Entity\ProjectInfo;
use App\Entity\User;
use App\Form\CreateFileFormType;
use App\Form\CreateFolderFormType;
use App\Service\ProjectWorkService;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectWorkController extends AbstractController
{
    /**
     * @Route(path="/project/{id}/{projectName}", methods={"GET", "POST"})
     * @param Request $request
     * @param $id
     * @param $projectName
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ProjectPage(Request $request, int $id, string $projectName)
    {
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

        $newFileForm = $this->createForm(CreateFileFormType::class);
        $newFolderForm = $this->createForm(CreateFolderFormType::class);

        $newFileForm->handleRequest($request);
        if($newFileForm->isSubmitted()) {
            $data = $newFileForm->getData();
            if ($newProject = ProjectWorkService::CreateFile($em, $data['fileName'], '', $project)) {
                $project = $newProject;
            } else {
                return $this->redirect('/exception');
            }
        }

        $newFolderForm->handleRequest($request);
        if($newFolderForm->isSubmitted()) {
            $data = $newFolderForm->getData();
            if ($newProject = ProjectWorkService::CreateFolder($em, $data['folderName'], '', $project)) {
                $project = $newProject;
            } else {
                return $this->redirect('/exception');
            }
        }

        $info = ProjectWorkService::GetProjectInfoForWork($project, '');

        return $this->render('/project/p_view.html.twig', [
            'log' => $log,
            'user' => $user,
            'currentUser' => $currentUser,
            'newFolder' => $newFolderForm->createView(),
            'newFile' => $newFileForm->createView(),
            'informationList' => $info,
            'project' => $project,
        ]);
    }

    /**
     * @Route(path="/project/{id}/{projectName}/{path}", methods={"GET", "POST"}, requirements={"path"=".+(?=(\/[a-zA-Z0-9_.()]+\.[a-z]+)|\/)"})
     * @param Request $request
     * @param int $id
     * @param string $projectName
     * @param string $path
     */
    public function ProjectPath(Request $request, int $id, string $projectName, string $path)
    {
        dd($path = $path . '/', 'path');

        /**
         * @todo: release working with path of the project
         */
    }

    /**
     * @Route(path="/project/{id}/{projectName}/{fileName}", methods={"GET", "POST"}, requirements={"fileName"="[a-zA-Z0-9_.()]+\.[a-z]+"})
     * @param Request $request
     * @param int $id
     * @param string $projectName
     * @param string $fileName
     */
    public function ProjectFile(Request $request, int $id, string $projectName, string $fileName)
    {
        dd($fileName, 'file');

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
        dd($path = $path . '/', $fileName, 'path with file name');
        /**
         * @todo: release working with path and file
         */
    }
}