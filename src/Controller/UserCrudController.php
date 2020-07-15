<?php


namespace App\Controller;


use App\Entity\User;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserCrudController extends AbstractController
{
    /**
     * @Route(path="/users_list", name="userList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userList(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $log = UserDataService::isLogged($user);

        $userList = $em->getRepository(User::class)->findAll();
        $userList = UserDataService::getOnlyAnotherUsers($user, $userList);
        $userInfo = UserDataService::getAnotherUsersInfo($userList);

        return $this->render('user/u_list.html.twig', [
            'log' => $log,
            'user' => $user,
            'informationList' => $userInfo,
        ]);

    }
}