<?php


namespace App\Controller;


use App\Entity\ProjectInfo;
use App\Form\ProjectCreatingFormType;
use App\Service\ProjectDataService;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectCreatingController extends AbstractController
{
    /**
     * @Route(path="/project/create", name="creating")
     * @param Request $request
     */
    public function creatingProjectForm (Request $request)
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

            if($pushToDBSuccesfull and $createDir)
            {
                $route = '/user/' . $user->getId() . '/' . $project->getProjectName();
            } else {
                $route = '/exception';
            }

            dd($route);

            return $this->redirectToRoute($route);

        }

        $log = UserDataService::isLogged($user);

        return $this->render('project/p_create.html.twig', [
            'user' => $user,
            'log' => $log,
            'projectCreate' => $form->createView(),
        ]);

    }
}