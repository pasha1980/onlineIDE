<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\PasswordChangeFormType;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use App\Service\UserDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($user = $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'log' => false,
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $name = $user->getNickname();
            $newName = str_replace(' ', '_', $name);
            $user->setNickname($newName);

            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $path = str_replace('/src/Controller', '/Users/', __DIR__);
            $createDir = mkdir($path . $user->getNickname(), 0777, false);
            if(!$createDir)
            {
                return $this->redirectToRoute('/exception');
            }

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'log' => false,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route(path="/pass_change")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function passwordChange(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $log = UserDataService::isLogged($user);
        if(!$log) {
            return $this->redirect('/');
        }

        $form = $this->createForm(PasswordChangeFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();

            if (password_verify($data['oldPassword'], $user->getPassword())) {
                if ($data['newPassword'] === $data['repeatNewPassword']) {
                    $hash = password_hash($data['newPassword'], 3);
                    $user->setPassword($hash);
                    $user->setUpdatedAt(new \DateTime('now'));
                    $em->persist($user);
                    $em->flush();
                    return $this->redirect('/user/' . $user->getId());

                } else {
                    return $this->render('security/password_change.html.twig', [
                        'log' => $log,
                        'user' => $user,
                        'form' => $form->createView(),
                        'warning' => 'New passwords do not match',
                    ]);
                }
            } else {
                return $this->render('security/password_change.html.twig', [
                    'log' => $log,
                    'user' => $user,
                    'form' => $form->createView(),
                    'warning' => 'Wrong user password entered',
                ]);
            }
        }

        return $this->render('security/password_change.html.twig', [
            'log' => $log,
            'user' => $user,
            'form' => $form->createView(),
            'warning' => null,
        ]);

    }

}