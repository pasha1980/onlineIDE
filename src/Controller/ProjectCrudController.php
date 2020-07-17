<?php


namespace App\Controller;


use App\Entity\ProjectInfo;
use App\Entity\User;
use App\Form\ProjectCreatingFormType;
use App\Service\ProjectDataService;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectCrudController extends AbstractController
{
    /**
     * @Route(path="/project/create", name="creating")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function creatingProject (Request $request)
    {
        $user= $this->get('security.token_storage')->getToken()->getUser();
        $project = new ProjectInfo();
        $form = $this->createForm(ProjectCreatingFormType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $config = $form->get('projectType')->getData();
            $em = $this->getDoctrine()->getManager();

            $pushToDBSuccesfull = ProjectDataService::pushCreatedProjectToDB($em, $project, $user, $config);

            $createDir = ProjectDataService::createProjectFolder($project, $user);

            if($pushToDBSuccesfull and $createDir) {
                $route = '/user/' . $user->getId() . '/' . $project->getProjectName();
            } else {
                $route = '/exception';
            }

            return $this->redirect($route);
        }

        $log = UserDataService::isLogged($user);

        return $this->render('project/p_create.html.twig', [
            'user' => $user,
            'log' => $log,
            'projectCreate' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/user/{id}/my_project_list", name="project_list", methods={"GET"})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function MyProjectList(Request $request, $id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $verifyUser = UserDataService::verifyUser($user, $id);
        $log = UserDataService::isLogged($user);

        if(!$log) {
            return $this->redirect('/');
        }

        if(!$verifyUser) {
            $route = '/user/' . $user->getId() . '/my_project_list';
            return $this->redirect($route);
        }

        $projectList = $em->getRepository(ProjectInfo::class)->findBy(['user'=>$user]);

        $projectInfo = ProjectDataService::getProjectInfoForListing($projectList);

        $isProjectExist = false;
        if($projectInfo != []) {
            $isProjectExist = true;
        }

        return $this->render('project/my_p_list.html.twig', [
            'log' => $log,
            'informationList' => $projectInfo,
            'user' => $user,
            'isProjectsExist' => $isProjectExist,
        ]);
    }

    /**
     * @Route(path="/user/{id}/project_list", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function UsersProjectList(Request $request, $id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $log = UserDataService::isLogged($user);

        if ($log) {
            $verifyUser = UserDataService::verifyUser($user, $id);
            if($verifyUser) {
                $route = '/user/' . $user->getId() . '/my_project_list';
                return $this->redirect($route);
            }
        }

        $anotherUser = $em->getRepository(User::class)->find($id);
        $projectList = $em->getRepository(ProjectInfo::class)->findBy(['user'=>$anotherUser]);
        $projectInfo = ProjectDataService::getProjectInfoForListing($projectList);

        foreach ($projectInfo as $key => $value)
        {
            if ($value['openclose']=='Close') {
                unset($projectInfo[$key]);
            }
        }

        $isProjectExist = false;
        if($projectInfo != []) {
            $isProjectExist = true;
        }

        return $this->render('/project/p_list.html.twig', [
            'log' => $log,
            'user' => $user,
            'informationList' => $projectInfo,
            'isProjectsExist' => $isProjectExist,
            'currentUser' => $anotherUser,
        ]);

    }

}