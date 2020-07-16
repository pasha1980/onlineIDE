<?php


namespace App\Controller;


use App\Entity\ProjectInfo;
use App\Entity\User;
use App\Form\EndRegistrationFormType;
use App\Form\ProjectCreatingFormType;
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

    /**
     * @Route(path="/user/{id}", methods={"GET"})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userPage(Request $request, $id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $log = UserDataService::isLogged($user);

        if(!$log) {
            return $this->redirect('/');
        }

        $verifyUser = UserDataService::verifyUser($user, $id);
        if ($verifyUser) {
            $currentUser = $user;
        } else {
            $em = $this->getDoctrine()->getManager();
            $currentUser = $em->getRepository(User::class)->find($id);
        }

        return $this->render('user/u_page.html.twig', [
            'log' => $log,
            'user' => $currentUser,
            'verify' => $verifyUser,
        ]);
    }

    /**
     * @Route(path="/user/{id}/edit", name="user_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editUserData(Request $request, $id)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $log = UserDataService::isLogged($user);

        if(!$log) {
            return $this->redirect('/');
        }

        $verifyUser = UserDataService::verifyUser($user, $id);

        if(!$verifyUser) {
            $route = '/user/' . $user->getId() . '/edit';
            return $this->redirect($route);
        }

        $form = $this->createForm(EndRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect('/user/' . $user->getId());
        }

        return $this->render('registration/end_register.html.twig', [
            'log' => $log,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}