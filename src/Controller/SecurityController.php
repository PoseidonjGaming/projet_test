<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    #[Route("/login", name: "app_login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('serie');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        

        return $this->render('security/login.html.twig', ['error' => $error]);
    }


    #[Route("/gerer_user", name: "gerer_user")]
    public function gereruser(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_super_admin');
        $rep = $entityManager->getRepository(User::class);
        $lesUsers = $rep->findAll();

        $user = new User();
        if (isset($_POST['ID'])) {
            $searchUser = $rep->findUnUser($_POST['ID']);
            if ($searchUser != null) {
                $user = $searchUser;
            }
        }

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        $error = ' ';

        if ($form->isSubmitted() && $form->isValid()) {


            $roles = [];
            dump($_POST);
            if ($form->get('password')->getData() != $_POST['password']) {
                return $this->render('security/gerer_user.html.twig', [
                    'user' => $lesUsers,
                    'formUser' => $form->createView(),
                    'error' => "Les mots de passe ne correspondent pas"

                ]);
            } else {
                $encoded = $encoder->hashPassword($user, $form->get('password')->getData());
                $user->setPassword($encoded);
                if (isset($_POST['roleAdmin'])) {
                    array_push($roles, 'ROLE_admin');
                }
                if (isset($_POST['roleSuperAdmin'])) {
                    array_push($roles, 'ROLE_super_admin');
                }
                $user->setRoles($roles);
                dump($user);
                $entityManager->persist($user);
                $entityManager->flush();
            }

            return $this->redirectToRoute('gerer_user');
        }

        return $this->render('security/gerer_user.html.twig', [
            'user' => $lesUsers,
            'formUser' => $form->createView(),
            'error' => $error

        ]);
    }




    #[Route("/supprimer_user/{id}", name: "supprimer_user")]
    public function supprimer_user($id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $user = $entityManager->getRepository(User::class)->findUnUser($id);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('gerer_user');
    }

    #[Route("/supprimer_users", name: "supprimer_user")]
    public function supprimer_users(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $tab = array_keys($_GET);
        $test = [];
        foreach ($tab as $int) {

            if ($int != "checkall") {
                $user = $entityManager->getRepository(User::class)->findUnUser($int);
                $entityManager->remove($user);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('gerer_user');
    }
}
