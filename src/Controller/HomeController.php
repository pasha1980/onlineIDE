<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route(path="/", name="home")
     */
    public function home(Request $request)
    {
        $log = false;
        $user= $this->get('security.token_storage')
            ->getToken()
            ->getUser();
        if ($user != 'anon.')
        {
            $log = true;
        }
        return $this->render('/home/home_page.html.twig', [
            'log' => $log,
            'user' => $user,
        ]);
    }

}