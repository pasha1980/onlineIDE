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

        if(!$project) {
            return $this->redirect('/user/' . $currentUser->getId());
        }

        if ($user === $currentUser) {
            $legal = true;
        } else {
            $legal = false;
        }

        if(!$project->getIsOpen() && !$legal) {
            return $this->redirect('/user/' . $currentUser->getId());
        }

        $em = $this->getDoctrine()->getManager();

        $newFileForm = $this->createForm(CreateFileFormType::class);
        $newFolderForm = $this->createForm(CreateFolderFormType::class);

        $newFileForm->handleRequest($request);
        if($newFileForm->isSubmitted()) {
            $data = $newFileForm->getData();
            if ($newProject = ProjectWorkService::CreateFolder($em, $data['folderName'], $project)) {

            }

        }

        $newFolderForm->handleRequest($request);
        if($newFolderForm->isSubmitted()) {
            $data = $newFolderForm->getData();
            dd($data);
        }

        return $this->render('/project/p_view.html.twig', [
            'log' => $log,
            'user' => $user,
            'currentUser' => $currentUser,
            'newFolder' => $newFolderForm->createView(),
            'newFile' => $newFileForm->createView(),
//            'info' => $info,
        ]);
    }
}