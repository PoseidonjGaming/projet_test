<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Serie;
use App\Entity\Saison;
use App\Entity\Acteur;
use App\Entity\Personnage;
use App\Entity\Episode;
use App\Form\SerieFormType;
use App\Form\ImportFormType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Service\Aide;
use Symfony\Component\HttpFoundation\JsonResponse;



class SerieController extends AbstractController
{
    /**
     * @Route("/serie", name="serie")
     */
    public function index(): Response
    {
       
        $rep=$this->getDoctrine()->getRepository(Serie::class);  
        $lesSerie=$rep->findAll();
        
        foreach($lesSerie as $serie){
            $resume=$serie->getResume();
            $resumeM=substr($resume, 0, 150).'...';
            $serie->setResume($resumeM);
            
        }

        return $this->render('serie/index.html.twig', [
            'serie' => $lesSerie,
           
        ]);
    }

    /**
     * @Route("/ajout/series", name="ajout_series")
     */
    public function ajout_series(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
       
        for($i=0; $i<$_POST['maxSerie']; $i++){
            $serie=new Serie();

            if($_POST['inputNom_'.$i]!=''){
                $serie->setNom($_POST['inputNom_'.$i]);
            }
            if($_POST['inputResume_'.$i]!=''){
                $serie->setResume($_POST['inputResume_'.$i]);
            }
            if($_POST['inputDate_'.$i]!=''){
                $date=new \DateTime($_POST['inputDate_'.$i]);
               
                $serie->setDateDiff($date);
            }
            if($_POST['inputSaison_'.$i]!=''){
               
                for($sa=0; $sa<$_POST['inputSaison_'.$i]; $sa++){
                    $saison=new Saison();
                    $saison->setNumero($sa+1);
                    $saison->setNbEpisode(0);

                    $entityManager->persist($saison);
                    $serie->addSaison($saison);
                }
                
            }
            if($_FILES['inputFile_'.$i]['name']!=''){
                move_uploaded_file($_FILES['inputFile_'.$i]['tmp_name'],$this->getParameter('photo_directory').'/photo/'.$_FILES['inputFile_'.$i]['name']);
                $serie->setAffiche($_FILES['inputFile_'.$i]['name']);

            }
            if($_POST['nbPerso_'.$i]!=''){
                for($p=0; $p<$_POST['nbPerso_'.$i]; $p++){
                    
                    $perso=$this->getDoctrine()->getRepository(Personnage::class)->findUnPersonnageByActeur($_POST['persoNom_'.$i.'_'.$p],$_POST['acteur_'.$i.'_'.$p]);
                    dump($perso);
                    if($perso==null){
                        $perso=new Personnage();
                        $perso->setActeur($this->getDoctrine()->getRepository(Acteur::class)->findUnActeur($_POST['acteur_'.$i.'_'.$p]));
                        $perso->setSerie($serie);
                        $entityManager->persist($saison);
                        
                    }
                }
            }
            if($_POST['inputURL_'.$i]!=''){
                $serie->setUrlBa($_POST['inputURL_'.$i]);
            }
            $entityManager->persist($serie);
            
        }
        $entityManager->flush();
        return $this->redirectToRoute('gerer_serie');
    }

    /**
     * @Route("/detail_serie/{id}", name="detail_serie")
     */
    public function detail_serie($id): Response
    {
        $rep=$this->getDoctrine()->getRepository(Serie::class);
        $serie=$rep->findUneSerie($id);
        $url=$serie->getUrlBa();
        $ch_url1=substr($url,0,13);
        $ch_url3=substr($url,16);
        $embed_url=$ch_url1."be.com"."/embed".$ch_url3;
        

        return $this->render('serie/detail_serie.html.twig', [
            'serie'=>$serie,
            'url'=>$embed_url,
            'youtube'=>$ch_url1
        ]);
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/gerer_serie", name="gerer_serie")
    */
    public function gererserie(Request $request): Response
    {         
        $rep=$this->getDoctrine()->getRepository(Serie::class);
        $lesSerie=$rep->findAll();
        
        $serie = new Serie();
        
        if(isset($_POST['ID'])){
            $searchSerie=$rep->findUneSerie($_POST['ID']);
            if($searchSerie!=null){
                $serie=$searchSerie;
            }
        }
        
        $acteurs=$this->getDoctrine()->getRepository(Acteur::class)->findAll(); 
        $form = $this->createForm(SerieFormType::class,$serie);
        $form->handleRequest($request);
        $error=' ';
        
        if($form->isSubmitted() && $form->isValid()){
            
            if($form->get('photo')->getData()!= null && $searchSerie==null){
                $images=$form->get('photo')->getData();
                $imgExt=$images->guessClientExtension();
                $ext=array('png','jpeg','jpg','gif','svg');
                $fileName =$images->getClientOriginalName();
                if(in_array($imgExt,$ext,$strict=false)){
                    $images->move($this->getParameter('photo_directory').'/photo',$fileName);
                }
                $serie->setAffiche($fileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($serie);
            
            for($i=0;$i<count($_POST)-2;$i++){
               if(isset($_POST["acteur_".$i]) && isset($_POST['persoNom_'.$i])){
                    if($_POST['acteur_'.$i]!=''&&$_POST['persoNom_'.$i]!=''){
                        
                        $acteur=$this->getDoctrine()->getRepository(Acteur::class)->findUnActeur($_POST['acteur_'.$i]);
                        
                        $personnage=new Personnage();
                        $personnage->setActeur($acteur)->setSerie($serie)->setNom($_POST['persoNom_'.$i]);
                        
                        $entityManager->persist($personnage);
                                
                    }
               }
            }
            $entityManager->flush();
            
            return $this->redirectToRoute('gerer_serie');
        }
       
        

        foreach($lesSerie as $uneSerie){
            
            $uneSerie->setResume(str_replace("'","\'",$uneSerie->getResume()));
            $uneSerie->setResume(str_replace("\r\n"," ",$uneSerie->getResume()));   
        }
        return $this->render('serie/gerer_serie.html.twig', [
            'serie' => $lesSerie,
            "formSerie" => $form->createView(),
            'error'=>$error,
            'titre'=>'Nouvelle série',
            'acteurs'=>$acteurs
          
        ]);
    }

    

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_serie/{id}", name="supprimer_serie")
    */
    public function supprimer_serie($id): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $serie=$entityManager->getRepository(Serie::class)->findUneSerie($id);

        
        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->redirectToRoute('gerer_serie');
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_series", name="supprimer_series")
     */
    public function supprimer_series(): Response
    {
        $tab=array_keys($_GET);
        
        $entityManager=$this->getDoctrine()->getManager();
        foreach($tab as $int){
            
            if($int != "checkall"){
                $serie=$entityManager->getRepository(Serie::class)->findUneSerie($int);
                if(file_exists($serie->getAffiche())){
                    unlink($this->getParameter('photo_directory').'/photo//'.$serie->getAffiche());
                }
                
                $entityManager->remove($serie);
                $entityManager->flush();
            }

            
        }
        return $this->redirectToRoute('gerer_serie');
    }

    
   
    

    /**
     * @Route("/test", name="test")
     */
    public function test(Request $request, Aide $aide): Response
    { 
        
        dump($_GET,$_POST);
        
        $series=$this->getDoctrine()->getRepository(Serie::class)->findAll();
        $acteurs=$this->getDoctrine()->getRepository(Acteur::class)->findAll();
        //dump($series[0]->dataJson());
        return $this->render('home/test.html.twig', [
            'acteurs'=>$acteurs,
            "series"=>$series
        ]);
        
        
    }
    /**
     * @Route("/test2", name="test2")
     */
    public function test2(Request $request): Response
    { 
        
        
        
        $Episode = $this->getDoctrine()->getRepository(Episode::class)->findAll();
        
        
        return $this->render('home/test2.html.twig', [
           'episodes'=>$Episode
        ]);
    }

    /**
     * @Route("/menuJSON", name="menuJSON")
     */
    public function menuJSON(): JsonResponse
    {
        
        if($_GET['type']=="episode"){
            $items=$this->getDoctrine()->getRepository(Episode::class)->findAll();
        }
        elseif($_GET['type']=='serie'){
            $items=$this->getDoctrine()->getRepository(Serie::class)->findAll();
        }
        elseif($_GET['type']=='acteur'){
            $items=$this->getDoctrine()->getRepository(Acteur::class)->findAll();
        }
        else{
            $items=$this->getDoctrine()->getRepository(Personnage::class)->findAll();
        }
        
        
        $data = [];
        foreach($items as $unItem){
            $data[]=$unItem->dataJson();
        }
        
        
        return new JsonResponse($data, Response::HTTP_OK);

       
    }
}
