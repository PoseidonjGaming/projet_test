<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Acteur;
use App\Form\ActeurFormType;
use Symfony\Component\Filesystem\Filesystem;



class ActeurController extends AbstractController
{
    /**
     * @Route("/ajout/acteurs", name="ajout_acteurs")
     */
    public function ajout_acteurs(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
       
        for($i=0; $i<$_POST['maxActeur']; $i++){
            $acteur=new Acteur();

            if($_POST['inputNom_'.$i]!=''){
                $acteur->setNom($_POST['inputNom_'.$i]);
            }
            if($_POST['inputPrenom_'.$i]!=''){
                $acteur->setPrenom($_POST['inputPrenom_'.$i]);
            }
            
            
            $entityManager->persist($acteur);
            
        }
        $entityManager->flush();
        return $this->redirectToRoute('gerer_acteurs');
        
    }
    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_acteur/{id}", name="supprimer_acteur")
     */
    public function supprimer_acteur($id): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $acteur=$entityManager->getRepository(Acteur::class)->findUnActeur($id);
       

        $entityManager->remove($acteur);
        $entityManager->flush();

        return $this->redirectToRoute('gerer_acteurs');
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_acteurs", name="supprimer_acteurs")
     */
    public function supprimer_acteurs(): Response
    {
        $tab=array_keys($_GET);
        $entityManager=$this->getDoctrine()->getManager();
        foreach($tab as $int){
            
            if($int != "checkall"){
                $episode=$entityManager->getRepository(Acteur::class)->findUnActeur($int);
                $entityManager->remove($episode);
                $entityManager->flush();
                
            }
           

            
        }
        return $this->redirectToRoute('gerer_acteurs');
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/gerer_acteurs", name="gerer_acteurs")
     */
    public function gerer_acteurs(Request $request): Response
    {
        $rep=$this->getDoctrine()->getRepository(Acteur::class);  
        $acteurs=$rep->findAll();
        
        $acteur=new Acteur();
        if(isset($_POST['ID'])){
            $searchActeur=$rep->findUnActeur($_POST['ID']);
            
            if($searchActeur!=null){
                $acteur=$searchActeur;
            }
        }
        
        $form = $this->createForm(ActeurFormType::class,$acteur);
        $form->handleRequest($request);
        $error=' ';
        
        
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($acteur);
            $entityManager->flush();

            return $this->redirectToRoute('gerer_acteur');
            
        }
       
        return $this->render('acteur/gerer_acteur.html.twig', [
            'acteurs'=> $acteurs,
            'formActeur'=>$form->createView() 
        ]);

        
    }
     
    /**
     * @Route("/export_acteur/{fic}", name="export_acteur")
     */
    public function export_acteur(Request $request,$fic): Response
    { 
       
       
      
        
    }
}
