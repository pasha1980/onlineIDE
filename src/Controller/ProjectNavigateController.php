<?php


namespace App\Controller;


use App\Entity\ProjectInfo;
use App\Entity\User;
use App\Form\CreateFileFormType;
use App\Form\CreateFolderFormType;
use App\Service\ProjectNavigateService;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectNavigateController extends AbstractController
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
        return $this->ProjectPath($request, $id, $projectName, null);
    }

    /**
     * @Route(path="/project/{id}/{projectName}/{path}", methods={"GET", "POST"}, requirements={"path"=".+(?=(\/[a-zA-Z0-9_.()]+\.[a-z]+)|\/)"})
     * @param Request $request
     * @param int $id
     * @param string $projectName
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ProjectPath(Request $request, int $id, string $projectName, ?string $path)
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


        if (!ProjectNavigateService::isPathValid($project, $path)) {
            return $this->redirect('/project/' . $currentUser->getId() . '/' . $project->getProjectName());
        }

        $newFileForm = $this->createForm(CreateFileFormType::class);
        $newFolderForm = $this->createForm(CreateFolderFormType::class);

        $newFileForm->handleRequest($request);
        if($newFileForm->isSubmitted()) {
            $data = $newFileForm->getData();
            if ($newProject = ProjectNavigateService::CreateFile($em, $data['fileName'], $path, $project)) {
                $project = $newProject;
            } else {
                return $this->redirect('/exception');
            }
        }

        $newFolderForm->handleRequest($request);
        if($newFolderForm->isSubmitted()) {
            $data = $newFolderForm->getData();
            if ($newProject = ProjectNavigateService::CreateFolder($em, $data['folderName'], $path, $project)) {
                $project = $newProject;
            } else {
                return $this->redirect('/exception');
            }
        }

        $info = ProjectNavigateService::GetProjectInfoForWork($project, $path);

        $path .= '/';
        if ($path) {
            $path = '/' . $path;
        }
        return $this->render('/project/p_view.html.twig', [
            'log' => $log,
            'user' => $user,
            'currentUser' => $currentUser,
            'newFolder' => $newFolderForm->createView(),
            'newFile' => $newFileForm->createView(),
            'informationList' => $info,
            'project' => $project,
            'currentPath' => '/project/' . $currentUser->getId() . '/' . $project->getProjectName() . $path,
        ]);

    }
}