<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Acteur;
use App\Form\ActeurFormType;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\Filesystem\Filesystem;
use PhpOffice\PhpSpreadsheet\Writer\Ods as Write;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
     * @Route("/import_acteur/{fic}", name="import_acteur")
     */
    public function import_acteur(Request $request,$fic): Response
    { 
        ini_set('max_execution_time', 100000);
        $repActeur=$this->getDoctrine()->getRepository(Acteur::class);
        $entityManager = $this->getDoctrine()->getManager();
               
        $array_nom=explode(",", $fic);
        $fic='';
       
    
        for($i=1; $i < count($array_nom); $i++){
            if($i==count($array_nom) || count($array_nom)==2 ){
                $fic=$fic.$array_nom[$i];
            }
            else{
                $fic=$fic.$array_nom[$i].",";
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
           $acteur=new Acteur();
            for($col = 1; $col <= $maxColId; $col++){
                $value = $worksheet->getCellByColumnAndRow($col,$ligne)->getValue();
               
                if($value!=null){
                
                    if($ligne==1){
                        $titreCol[$value]=$col;
                        
                    }
                    else{
                        
                        if($col==$titreCol['Nom']){
                            $acteur->setNom($value);  
                        }
                        if($col==$titreCol['Prénom']){
                            $acteur->setPrenom($value);  
                        }
                        dump($acteur);
                        if($col==$maxColId){
                            $entityManager->persist($acteur);
                            $entityManager->flush();
                        }
                        
                    }
                }
            }
        }
        unlink($this->getParameter('photo_directory')."import/".$array_nom[0]);
        if($fic==""){
            return $this->redirectToRoute('gerer_serie');
        }
        else{
            return $this->redirectToRoute('carrefour',array('tab'=>$fic));
        }
        /*return $this->render('home/test.html.twig', [
            
        ]);*/
    }
    /**
     * @Route("/export_acteur/{fic}", name="export_acteur")
     */
    public function export_acteur(Request $request,$fic): Response
    { 
        ini_set('max_execution_time', 100000);
        $repActeur=$this->getDoctrine()->getRepository(Acteur::class);
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
        
        $data=[['Nom','Prénom']];
        if($_GET==[] && $array_nom[0]=='acteur'){
            $acteurs=$repActeur->findAll();
        }
        elseif($_GET!=[]){
            $tab=array_keys($_GET);
            $acteurs=[];
            foreach($tab as $int){
                        
                if($int != "checkall"){
                    $acteur=$entityManager->getRepository(Acteur::class)->findUnActeur($int);
                    array_push($acteurs,$acteur);
                }
                
            }
           
        }
        else{
            $acteurs=null;
        }

        if($acteurs!=null){
            foreach($acteurs as $unActeur){
            
                $acteur=[$unActeur->getNom(),$unActeur->getPrenom()];
                array_push($data, $acteur);
            }
            
            $spread->getActiveSheet()->fromArray($data,NULL,'A1');
        
            $writer->save($this->getParameter('photo_directory').'export/acteur.ods');    
       
            if($fic1==''){
                $fic1="Export donnée";
            }
            return $this->redirectToRoute('carrefour_export',array('tab'=>$fic1));
        }
        else{
            return $this->redirectToRoute('gerer_acteur');
        }
      
        
    }
}
