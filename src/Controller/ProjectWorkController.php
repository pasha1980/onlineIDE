<?php


namespace App\Controller;


use App\Entity\ProjectInfo;
use App\Entity\User;
use App\Service\ProjectDataService;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectWorkController extends AbstractController
{
    /**
     * @Route(path="/user/{id}/{projectName}", methods={"GET", "POST"})
     * @param Request $request
     * @param $id
     * @param $projectName
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ProjectPage(Request $request, $id, $projectName)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
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

        /**
         * @todo: !!! view project and create file/folder
         */

        die;
    }
}