<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\Serie;
use App\Entity\Saison;
use App\Entity\Acteur;
use App\Entity\Personnage;
use App\Entity\Episode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Service\Aide;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('serie');
    }

    /**
     * @isGranted("role_admin")
     * @Route("/generationuser", name="generationuser")
     */
    public function generationUser(UserPasswordEncoderInterface $encoder): Response
    {
        $em=$this->getDoctrine()->getManager();
        $user= new User();
        $encoded= $encoder->encodePassword($user,"1234");
        //var_dump($encoded);
        $user->setNom("Admin");
        $user->setPassword($encoded);
        $user->setRoles(["role_admin"]);
        dump($user);
        $em->persist($user);
        $em->flush();
        
        return $this->redirectToRoute('serie');
    }

   

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/import", name="import")
     */
    public function import(Aide $aide): Response
    {
        
        if($_FILES['fichierSerie']['tmp_name'][0]!=""){
            $i=0;
            foreach($_FILES['fichierSerie']['name'] as $int){
                $array_nom=explode('.',$int);
            
                if($array_nom[1]=='jpg'){
                    copy($_FILES['fichierSerie']['tmp_name'][$i],$this->getParameter('photo_directory').'/photo//'.$int);
                }
                else{
                    copy($_FILES['fichierSerie']['tmp_name'][$i],$this->getParameter('photo_directory').'/import//'.'serie.ods');
                }
                
                $i++;
                
            }
            $aide->import_serie();
           
        }
        if($_FILES['fichierEpisode']['tmp_name']!=''){
           
            copy($_FILES['fichierEpisode']['tmp_name'],$this->getParameter('photo_directory').'/import//'.'episode.ods');
            $aide->import_episode();
        } 
        if($_FILES['fichierActeur']['tmp_name']!=""){
        
            copy($_FILES['fichierActeur']['tmp_name'],$this->getParameter('photo_directory').'/import//'.'acteur.ods');
            $aide->import_acteur();
            
            
        }
        if($_FILES['fichierPersonnage']['tmp_name']!=""){
        
            copy($_FILES['fichierPersonnage']['tmp_name'],$this->getParameter('photo_directory').'/import//'.'personnage.ods');
            $aide->import_personnage();
            
        }
        
        
        /*return $this->render('home/test.html.twig', [
            
          
        ]);*/
        
        return $this->redirectToRoute('serie');
        
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/export", name="export")
     */
    public function export(Aide $aide): Response
    {
        
        if(isset($_POST['series'])){
           $affiches=$aide->export_serie($_GET);
        }
        if(isset($_POST['episode'])){
           $aide->export_episode($_GET);
        }
        if(isset($_POST['acteur'])){
           $aide->export_acteur($_GET);
        }
        if(isset($_POST['personnage'])){
           $aide->export_personnage($_GET);
        }
        
        
        $zip=new \ZipArchive();
        $zip->open($this->getParameter('photo_directory').'/export//'.'Export_donnée.zip',\ZipArchive::CREATE);
        
        
        
        $zip->addFile($this->getParameter('photo_directory').'/export//'.'episode.ods',"episode.ods");
        $zip->addFile($this->getParameter('photo_directory').'/export//'.'acteur.ods','acteur.ods');
        $zip->addFile($this->getParameter('photo_directory').'/export//'.'personnage.ods','personnage.ods');
        

        if(file_exists($this->getParameter('photo_directory').'/export//'.'serie.ods')){
            $zip->addEmptyDir("Serie");
            $zip->addFile($this->getParameter('photo_directory').'/export//'.'serie.ods','Serie/'.'serie.ods');
            $dir=opendir($this->getParameter('photo_directory').'/photo//');
            while($file=readdir($dir)){
                foreach($affiches as $affiche){
                    
                    if(file_exists($this->getParameter('photo_directory').'/photo//'.$file) && $file!='.' && $file!='..' && $file==$affiche){
                        
                        $zip->addFile("photo/".$file,'Serie/'.$file);
                        
                    }
                }
                
            }
    
            closedir($dir);
        }
        
        $zip->close();
        
        if(file_exists($this->getParameter('photo_directory').'/export//'."Export_donnée.zip")){
            $response = new Response(file_get_contents($this->getParameter('photo_directory')."export/".'Export_donnée.zip'));
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition: attachment; filename=', 'Export_donnée.zip');
            
            unlink($this->getParameter('photo_directory').'/export//'.'Export_donnée.zip');
            if(file_exists($this->getParameter('photo_directory')."export/serie.ods")){
                unlink($this->getParameter('photo_directory')."export/serie.ods");
            }
            if(file_exists($this->getParameter('photo_directory')."export/episode.ods")){
                unlink($this->getParameter('photo_directory')."export/episode.ods");
            }
            if(file_exists($this->getParameter('photo_directory')."export/acteur.ods")){
                unlink($this->getParameter('photo_directory')."export/acteur.ods");
            }
            if(file_exists($this->getParameter('photo_directory')."export/personnage.ods")){
                unlink($this->getParameter('photo_directory')."export/personnage.ods");
            }
            return $response;
            
           
        }
        else{
           return $this->redirectToRoute('serie');
            
        }
    }

    
    
}
