<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Personnage;
use App\Entity\Serie;
use App\Entity\Acteur;
use App\Form\PersonnageFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Ods as Write;
use Symfony\Component\Filesystem\Filesystem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PersonnageController extends AbstractController
{
    /**
     * @Route("/ajout/personnages", name="ajout_personnages")
     */
   public function ajout_personnages(): Response
   {
       $entityManager = $this->getDoctrine()->getManager();
       dump($_POST);
      
       for($i=0; $i<$_POST['maxPerso']; $i++){
            $personnage=new Personnage();

            if($_POST['inputNom_'.$i]!=''){
               $personnage->setNom($_POST['inputNom_'.$i]);
            }
            if($_POST['inputActeur_'.$i]!=''){
                $repActeur=$this->getDoctrine()->getRepository(Acteur::class);
                $personnage->setActeur($repActeur->findUnActeur($_POST['inputActeur_'.$i]));
                
            }
            if($_POST['inputSerie_'.$i]!=''){
                $repSerie=$this->getDoctrine()->getRepository(Serie::class);
                
                $personnage->setSerie($repSerie->findUneSerie($_POST['inputSerie_'.$i]));
            }
            
           
           
            $entityManager->persist($personnage);
            dump($personnage);
       }
       $entityManager->flush();
       return $this->redirectToRoute('gerer_personnages');
      
       
   }
    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_personnage/{id}", name="supprimer_personnage")
     */
    public function supprimer_personnage($id): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $personnage=$entityManager->getRepository(Personnage::class)->findUnPersonnage($id);
       
        $id=$personnage->getActeur()->getId();
        $entityManager->remove($personnage);
        $entityManager->flush();
        
        if($_GET['route']=='gerer_personnage'){
            return $this->redirectToRoute('gerer_personnage',array('id'=>$id));
        }
        else{
            return $this->redirectToRoute('gerer_personnages');
        }
        
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_personnages", name="supprimer_personnages")
     */
    public function supprimer_personnages(): Response
    {
        $tab=array_keys($_GET);
        $entityManager=$this->getDoctrine()->getManager();
        dump($_GET);
        foreach($tab as $int){
    
            if($int != "checkall" && $int!="route" ){
                $personnage=$entityManager->getRepository(Personnage::class)->findUnPersonnage($int);
                $id=$personnage->getActeur()->getId();
                $entityManager->remove($personnage);
                
                
            }
           
            
            
        }
        $entityManager->flush();
       
        if($_GET['route']=='gerer_personnage'){
            return $this->redirectToRoute('gerer_personnage',array('id'=>$id));
        }
        else{
            return $this->redirectToRoute('gerer_personnages');
        }
       
       
        
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/gerer_personnage/{id}", name="gerer_personnage")
     */
    public function gerer_personnage(Request $request,$id): Response
    {
        $rep=$this->getDoctrine()->getRepository(Personnage::class);  
        $personnages=$rep->findPersonnages($id);

        
        $series=$this->getDoctrine()->getRepository(Serie::class)->findAll();
        
        
        $personnage=new Personnage();
        if(isset($_POST['ID'])){
            $searchPersonnage=$rep->findUnPersonnage($_POST['ID']);
            
            if($searchPersonnage!=null){
                $personnage=$searchPersonnage;
            }
        }
        
        $form = $this->createForm(PersonnageFormType::class,$personnage);
        $form->handleRequest($request);
        $error=' ';

        if ($form->isSubmitted() && $form->isValid()) {

           
            
            $personnage->setActeur($this->getDoctrine()->getRepository(Acteur::class)->findUnActeur($id));
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personnage);
            $entityManager->flush();
         
            return $this->redirectToRoute('gerer_personnage',array('id'=>$id));
            
        }

        return $this->render('personnage/gerer_personnage.html.twig', [
            'personnages'=> $personnages,
            'formPersonnage'=>$form->createView(),
            "id"=>$id,
            'route'=>'gerer_personnage',
            'series'=>$series
        ]);

        
    }
     /**
     * @IsGranted("ROLE_admin")
     * @Route("/gerer_personnages", name="gerer_personnages")
     */
    public function gerer_personnages(Request $request): Response
    {
        $rep=$this->getDoctrine()->getRepository(Personnage::class);  
        $personnages=$rep->findAll();
        $acteurs=$this->getDoctrine()->getRepository(Acteur::class)->findAll();
        $series=$this->getDoctrine()->getRepository(Serie::class)->findAll();
        
        $personnage=new Personnage();
        if(isset($_POST['ID'])){
            $searchPersonnage=$rep->findUnPersonnage($_POST['ID']);
            
            if($searchPersonnage!=null){
                $personnage=$searchPersonnage;
            }
        }
        
        $form = $this->createForm(PersonnageFormType::class,$personnage);
        $form->handleRequest($request);
        $error=' ';
        
        if ($form->isSubmitted() && $form->isValid()) {

            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personnage);
            $entityManager->flush();
            return $this->redirectToRoute('gerer_personnages');
            
            
        }

        return $this->render('personnage/gerer_personnage.html.twig', [
            'personnages'=> $personnages,
            'formPersonnage'=>$form->createView(),
            'route'=>'gerer_personnages',
            'acteurs'=>$acteurs,
            'series'=>$series
            
        ]);

        
    }

    /**
     * @Route("/import_personnage/{fic}", name="import_personnage")
     */
    public function import_personnage(Request $request,$fic): Response
    { 
        ini_set('max_execution_time', 100000);

        $entityManager = $this->getDoctrine()->getManager();
               
        $array_nom=explode(",", $fic);
        $fic1='';
       
    
        for($i=1; $i < count($array_nom); $i++){
        
            if($i==count($array_nom) || count($array_nom)==2 ){
                $fic1=$fic1.$array_nom[$i];
            }
            else{
                $fic1=$fic1.$array_nom[$i].",";
            }
            
        }
        $reader = new Ods(); 
        
        $reader->setReadDataOnly(TRUE);
       
        $spreadsheet = $reader->load($this->getParameter('photo_directory')."import/".$array_nom[0]);
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow(); 
        $maxCol = $worksheet->getHighestColumn(); 
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);
        

        $titreCol=array();

        for($ligne=1; $ligne <= $maxLigne; $ligne++){
           $personnage=new Personnage();
            for($col = 1; $col <= $maxColId; $col++){
                $value = $worksheet->getCellByColumnAndRow($col,$ligne)->getValue();
               
                if($value!=null){
                
                    if($ligne==1){
                        $titreCol[$value]=$col;
                        
                    }
                    else{
                        
                        if($col==$titreCol['Nom']){
                            $personnage->setNom($value);
                            
                        }
                        if($col==$titreCol['Acteur']){
                           $array_acteur=explode(' ',$value);
                           $acteur=$this->getDoctrine()->getRepository(Acteur::class)->findUnActeurByNom($array_acteur[0],$array_acteur[1]);
                           if($acteur==null){
                                $acteur=new Acteur();
                                $acteur->setNom($array_acteur[1]);
                                $acteur->setPrenom($array_acteur[0]);
                                $entityManager->persist($acteur);
                                $entityManager->flush();
                           }
                           $personnage->setActeur($acteur);
                           
                        }
                        if($col==$titreCol['Série']){
                            $serie=$this->getDoctrine()->getRepository(Serie::class)->findUneSerieByName($value);
                            if($serie==null){
                                $serie=new Serie();
                                $serie->setNom($value);
                                $entityManager->persist($serie);
                                $entityManager->flush();
                            }
                            $personnage->setSerie($serie);
                            
                         }
                        

                        if($col==$maxColId){
                            $entityManager->persist($personnage);
                            $entityManager->flush();
                            
                        }
                        
                    }
                }
            }
        }
        unlink($this->getParameter('photo_directory')."import/".$array_nom[0]);
        if($fic1==""){
            return $this->redirectToRoute('serie');
            
        }
        else{
            return $this->redirectToRoute('carrefour',array('tab'=>$fic1));
     
        }
        
    }

     /**
     * @Route("/export_personnage/{fic}", name="export_personnage")
     */
    public function export_personnage(Request $request,$fic): Response
    { 
        ini_set('max_execution_time', 100000);
        $repPerso=$this->getDoctrine()->getRepository(Personnage::class);
        $entityManager = $this->getDoctrine()->getManager();
               
        $array_nom=explode(",", $fic);
        $fic1='';
        
    
        for($i=1; $i < count($array_nom); $i++){
            if($i==count($array_nom)-1 || count($array_nom)==2 ){
                $fic1=$fic1.$array_nom[$i];
            }
            elseif($fic!=''){
                $fic1=$fic1.$array_nom[$i].",";
            }
            
            
        }
        $spread=new SpreadSheet();
        $writer = new Write($spread); 
        
        $data=[['Nom','Série','Acteur']];
       
        if($_GET==[] && $array_nom[0]=='personnage'){
            $personnages=$repPerso->findAll();
        }
        elseif($_GET!=[]){
            $tab=array_keys($_GET);
            $personnages=[];
            foreach($tab as $int){
                        
                if($int != "checkall"){
                    $personnage=$entityManager->getRepository(Personnage::class)->findUnPersonnage($int);
                    array_push($personnages,$personnage);
                }
                
            }
        }
        else{
            $personnages=null;
        }
        if($personnages!=null){
            foreach($personnages as $unPersonnage){
            
                $serie=[$unPersonnage->getNom(),$unPersonnage->getSerie()->getNom(),$unPersonnage->getActeur()->getPrenom().' '.$unPersonnage->getActeur()->getNom()];
                
    
                array_push($data, $serie);
            }
            $spread->getActiveSheet()->fromArray($data,NULL,'A1');
            
           
    
            
            $writer->save($this->getParameter('photo_directory').'export/personnage.ods');    
         
            if($fic1==''){
                $fic1="Export donnée";
                
            }
            
            return $this->redirectToRoute('carrefour_export',array('tab'=>$fic1));
        }
        else{
            return $this->redirectToRoute('gerer_personnages');
        }
        
        
    
    }
}
