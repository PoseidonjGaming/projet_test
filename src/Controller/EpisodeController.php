<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Episode;
use App\Entity\Saison;
use App\Entity\Serie;
use App\Form\EpisodeFormType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Ods as Write;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\Aide;



class EpisodeController extends AbstractController
{
    /**
     * @Route("/ajout/episodes", name="ajout_episodes")
     */
    public function ajout_episodes(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        dump($_POST);
       
        for($i=0; $i<$_POST['maxEpisode']; $i++){
            $episode=new Episode();

            if($_POST['inputNom_'.$i]!=''){
                $episode->setNom($_POST['inputNom_'.$i]);
            }
            if($_POST['inputResume_'.$i]!=''){
                $episode->setResume($_POST['inputResume_'.$i]);
            }
            if($_POST['inputDate_'.$i]!=''){
                $date=new \DateTime($_POST['inputDate_'.$i]);
                
                $episode->setDatePremDiff($date);
            }
            if($_POST['inputSerie_'.$i]!=''){
                $serie=$this->getDoctrine()->getRepository(Serie::class)->findUneSerie($_POST['inputSerie_'.$i]);
                
            }
            if($_POST['inputSaison_'.$i]!=''){
                $saison=$this->getDoctrine()->getRepository(Saison::class)->findUneSaisonByNum($_POST['inputSaison_'.$i], $serie->getId());
                if($saison==null){
                    $saison=new Saison();
                    $saison->setSerie($serie);
                    $saison->setNumero($_POST['inputSaison_'.$i]);
                    $saison->setNbEpisode(1);
                    $entityManager->persist($saison);
                }
                else{
                    $saison->setNbEpisode($saison->getNbEpisode()+1);
                }
                $episode->setSaison($saison);
                
            }
            
            $entityManager->persist($episode);
            
        }
        $entityManager->flush();
        return $this->redirectToRoute('gerer_episodes');
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_episode/{id}", name="supprimer_episode")
     */
    public function supprimer_episode($id): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $episode=$entityManager->getRepository(Episode::class)->findUnEpisode($id);
        $saisonId=$episode->getSaison()->getId();

        $entityManager->remove($episode);
        $entityManager->flush();

        return $this->redirectToRoute('gerer_episode',array('id'=> $saisonId));
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/supprimer_episodes", name="supprimer_episodes")
     */
    public function supprimer_episodes(): Response
    {
        $tab=array_keys($_GET);
        $entityManager=$this->getDoctrine()->getManager();
        foreach($tab as $int){
            
            if($int != "checkall"){
                $episode=$entityManager->getRepository(Episode::class)->findUnEpisode($int);
                $id=$episode->getSaison()->getId();
                $entityManager->remove($episode);
                $entityManager->flush();
                
            }
           

            
        }
        return $this->redirectToRoute('gerer_episode',array('id'=> $id));
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/gerer_episode/{id}", name="gerer_episode")
     */
    public function gerer_episode(Request $request, $id,Aide $aide, PaginatorInterface $paginator): Response
    {
        $rep=$this->getDoctrine()->getRepository(Episode::class);  
        $ListeEpisodes=$rep->findEpisodes($id);
        
        
        $episode=new Episode();
        if(isset($_POST['ID'])){
            $searchEpisode=$rep->findUnEpisode($_POST['ID']);
            
            if($searchEpisode!=null){
                $episode=$searchEpisode;
            }
        }
        
        $form = $this->createForm(EpisodeFormType::class,$episode);
        $form->handleRequest($request);
        $error=' ';

        if ($form->isSubmitted() && $form->isValid()) {

            
            $repSaison=$this->getDoctrine()->getRepository(Saison::class);
            $saison=$repSaison->findUneSaison($id);
            $serie=$saison->getSerie();
           
            $episode->setSaison($saison);
            $entityManager = $this->getDoctrine()->getManager();
            if($episode->getId()==null && !isset($_POST['last_season']) && (count($saison->getEpisodes())+1)>intval($saison->getNbEpisode())){
                $saison->setNbEpisode($saison->getNbEpisode()+1);
                
            }
            elseif(isset($_POST['last_season'])){
                $saisonNext=new Saison();
                $saisonNext->setNbEpisode(1);
                $saisonNext->setSerie($saison->getSerie());
                $entityManager->persist($saisonNext);
            }

            
            $entityManager->persist($episode);
            
            $entityManager->flush();

            return $this->redirectToRoute('gerer_episode',array('id'=> $id));
            
        }

        
            foreach($ListeEpisodes as $UnEpisode){
                
                $UnEpisode->setResume(str_replace("'","\'",$UnEpisode->getResume()));
                $UnEpisode->setResume(str_replace("\r\n"," ",$UnEpisode->getResume()));  
            }
            $episodes=$aide->pager($ListeEpisodes, $paginator,"saison.serie.id","asc", $request);
            dump($episodes);
        return $this->render('episode/gerer_episode.html.twig', [
            
            'episodes'=> $episodes,
            'formEpisode'=>$form->createView(),
            'id'=>$id
            
        ]);

        
    }
   
    /**
     * @IsGranted("ROLE_admin")
     * @Route("/gerer_episodes", name="gerer_episodes")
     */
    public function gerer_episodes(Request $request ,Aide $aide): Response
    {
        $rep=$this->getDoctrine()->getRepository(Episode::class);  
        $ListeEpisodes=$rep->findAll();
        $series=$this->getDoctrine()->getRepository(Serie::class)->findAll();
        $episode=new Episode();
        if(isset($_POST['ID'])){
            $searchEpisode=$rep->findUnEpisode($_POST['ID']);
            
            if($searchEpisode!=null){
                $episode=$searchEpisode;
            }
        }
        
        $form = $this->createForm(EpisodeFormType::class,$episode);
        $form->handleRequest($request);
        $error=' ';

        if ($form->isSubmitted() && $form->isValid()) {
            
            $repSaison=$this->getDoctrine()->getRepository(Saison::class);
            $saison=$repSaison->findUneSaisonByNum($_POST['saison'],$_POST['serie']);
            
            
            $entityManager = $this->getDoctrine()->getManager();
            if($episode->getId()==null && !isset($_POST['last_season']) && (count($saison->getEpisodes())+1)>intval($saison->getNbEpisode())){
                $saison->setNbEpisode($saison->getNbEpisode()+1);
                
            }
            elseif(isset($_POST['last_season'])){
                $saisonNext=new Saison();
                $saisonNext->setNbEpisode(1);
                $saisonNext->setSerie($saison->getSerie());
                $entityManager->persist($saisonNext);
            }

            
            $entityManager->persist($episode);
            
            $entityManager->flush();

        
            return $this->redirectToRoute('gerer_episodes');

            
        }

        
            foreach($ListeEpisodes as $UnEpisode){
                
                $UnEpisode->setResume(str_replace("'","\'",$UnEpisode->getResume()));
                $UnEpisode->setResume(str_replace("\r\n"," ",$UnEpisode->getResume()));  
            }
        
        return $this->render('episode/gerer_episode.html.twig', [
            
            'episodes'=> $ListeEpisodes,
            'series'=>$series,
            'formEpisode'=>$form->createView(),
           
            
        ]);

        
    }

    /**
     * @Route("/import_episode/{fic}", name="import_episode")
     */
    public function import_episode(Request $request,$fic): Response
    { 
        ini_set('max_execution_time', 100000);
        $repEp=$this->getDoctrine()->getRepository(Episode::class);
        $repSerie=$this->getDoctrine()->getRepository(Serie::class);
        $repSaison=$this->getDoctrine()->getRepository(Saison::class);
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
           $episode=new Episode();
            for($col = 1; $col <= $maxColId; $col++){
                $value = $worksheet->getCellByColumnAndRow($col,$ligne)->getValue();
               
                if($value!=null){
                    
                    if($ligne==1){
                        $titreCol[$value]=$col;
                        
                    }
                    else{
                        
                        if($col==$titreCol['Nom épisode']){
                            $episode->setNom($value);
                            
                        }
                        if($col==$titreCol['Résumé épisode']){
                           $episode->setResume($value);
                           
                        }
                        if($col==$titreCol['Date ep']){
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                            $episode->setDatePremDiff($date);
                        }
                        
                        if($col==$titreCol['nom série']){
                            $serie=$repSerie->findUneSerieByName($value);
                            if($serie==null){
                                $serieNew=new Serie();
                                $serieNew->setNom($value);
                                
                                $entityManager->persist($serieNew);
                                
                                
                            }
                        }
                        if($col==$titreCol['saison']){
                            $saison=$repSaison->findUneSaisonByNum($value,$serie->getId());
                            if($saison!=null){
                                if(count($saison->getNbEpisode())==$saison->getNbEpisode()){
                                    $saison->setNbEpisode($saison->getNbEpisode()+1);
                                    
                                }
                            }
                            else{
                                $saison=new Saison();
                                $saison->setNumero(1);
                                $saison->setNbEpisode(1);
                                $saison->setSerie($serie);
                                
                            }
                            $entityManager->persist($saison);
                            $episode->setSaison($saison);
                        }
                        
                        if($col==$maxColId){
                            $entityManager->persist($episode);
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
     * @Route("/export_episode/{fic}", name="export_episode")
     */
    public function export_episode(Request $request,$fic): Response
    { 
        ini_set('max_execution_time', 100000);
        $repEpisode=$this->getDoctrine()->getRepository(Episode::class);
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
        
        $data=[['Nom épisode','Résumé épisode','Date ep','nom série','saison']];
       
      
        if($_GET==[] && $array_nom[0]=='episode'){
            $episodes=$repEpisode->findAll();
        }
        elseif($_GET!=[]){
            $tab=array_keys($_GET);
            $episodes=[];
            foreach($tab as $int){
                        
                if($int != "checkall"){
                    $episode=$entityManager->getRepository(Episode::class)->findUnEpisode($int);
                    array_push($episodes,$episode);
                }
                
            }
        }
        else{
            $episodes=null;
        }
       
        if($episodes!=null){
            foreach($episodes as $unEpisode){
            
                $episode=[$unEpisode->getNom(),$unEpisode->getResume(),\PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($unEpisode->getDatePremDiff()->format('d/m/Y')),$unEpisode->getSaison()->getSerie()->getNom(),$unEpisode->getSaison()->getNumero()];
                
    
                array_push($data, $episode);
            }
            $spread->getActiveSheet()->fromArray($data,NULL,'A1');
           
            
            $writer->save($this->getParameter('photo_directory').'export/episode.ods');    
            
            if($fic1==''){
                $fic1="Export donnée";
            }
            
            return $this->redirectToRoute('carrefour_export',array('tab'=>$fic1));
        }
        else{
            return $this->redirectToRoute('gerer_episodes');
           
        
            
        }
        
        
            
        
        
    }
}
