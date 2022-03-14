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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
     
     /**
     * @IsGranted("ROLE_super_admin")
     * @Route("/gerer_user", name="gerer_user")
     */
    public function gereruser(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $rep=$this->getDoctrine()->getRepository(User::class);  
        $lesUsers=$rep->findAll();

        $user=new User();
        if(isset($_POST['ID'])){
            $searchUser=$rep->findUnUser($_POST['ID']);
            if($searchUser!=null){
                $user=$searchUser;
            }
        }
        
        $form = $this->createForm(UserFormType::class,$user);
        $form->handleRequest($request);
        $error=' ';

        if ($form->isSubmitted() && $form->isValid()) {

            
            $roles=[];
            dump($_POST);
            if($form->get('password')->getData()!=$_POST['password']){
                return $this->render('security/gerer_user.html.twig', [
                    'user' => $lesUsers,
                    'formUser'=>$form->createView(),
                    'error'=>"Les mots de passe ne correspondent pas"
                  
                ]);
            }
            else{
                $encoded= $encoder->encodePassword($user,$form->get('password')->getData());
                $user->setPassword($encoded);
                if(isset($_POST['roleAdmin'])){
                    array_push($roles,'ROLE_admin');
                }
                if(isset($_POST['roleSuperAdmin'])){
                    array_push($roles,'ROLE_super_admin');
                }
                $user->setRoles($roles);
                dump($user);
                $entityManagersaison = $this->getDoctrine()->getManager();
                $entityManagersaison->persist($user);
                $entityManagersaison->flush();
            }
            
            return $this->redirectToRoute('gerer_user');
           
        }

        return $this->render('security/gerer_user.html.twig', [
            'user' => $lesUsers,
            'formUser'=>$form->createView(),
            'error'=>$error
          
        ]);
    }

    

    
    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_user/{id}", name="supprimer_user")
     */
    public function supprimer_user($id): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $user=$entityManager->getRepository(User::class)->findUnUser($id);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('gerer_user');
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_users", name="supprimer_user")
     */
    public function supprimer_users(): Response
    {
        $tab=array_keys($_GET);
        $test=[];
        $entityManager=$this->getDoctrine()->getManager();
        foreach($tab as $int){
            
            if($int != "checkall"){
                $user=$entityManager->getRepository(User::class)->findUnUser($int);
                $entityManager->remove($user);
                $entityManager->flush();
            }

            
        }
        return $this->redirectToRoute('gerer_user');
    }

}
