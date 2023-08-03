<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Serie;
use App\Entity\Acteur;
use App\Entity\Personnage;
use App\Entity\Episode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\Aide;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->redirectToRoute('serie');
    }

    #[Route('/generationuser', name: "generationuser")]
    public function generationUser(UserPasswordHasherInterface  $encoder, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $encoded = $encoder->hashPassword($user, "1234");
        //var_dump($encoded);
        $user->setUsername("Admin");
        $user->setPassword($encoded);
        $user->setRoles(["ROLE_super_admin"]);
        dump($user);
        $entityManager->persist($user);
        $entityManager->flush();

        //return $this->redirectToRoute('serie');
        return $this->render('serie/index.html.twig', []);
    }


    #[Route("/users/json", name: "jsonUser")]
    public function jsonUser(UserPasswordHasherInterface $encoder): Response
    {
        //return $this->redirectToRoute('serie');
        return $this->render('serie/index.html.twig', []);
    }



    #[Route("/import", name: "import")]
    public function import(Aide $aide)
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        if ($_FILES['fichierSerie']['tmp_name'][0] != "") {
            $i = 0;
            foreach ($_FILES['fichierSerie']['name'] as $int) {
                $array_username = explode('.', $int);

                if ($array_username[1] == 'jpg') {
                    copy($_FILES['fichierSerie']['tmp_name'][$i], $this->getParameter('photo_directory') . '/photo//' . $int);
                } else {
                    copy($_FILES['fichierSerie']['tmp_name'][$i], $this->getParameter('photo_directory') . '/import//' . 'serie.ods');
                }

                $i++;
            }
            $aide->import_serie();
        }
        if ($_FILES['fichierEpisode']['tmp_name'] != '') {

            copy($_FILES['fichierEpisode']['tmp_name'], $this->getParameter('photo_directory') . '/import//' . 'episode.ods');
            $aide->import_episode();
        }
        if ($_FILES['fichierActeur']['tmp_name'] != "") {

            copy($_FILES['fichierActeur']['tmp_name'], $this->getParameter('photo_directory') . '/import//' . 'acteur.ods');
            $aide->import_acteur();
        }
        if ($_FILES['fichierPersonnage']['tmp_name'] != "") {

            copy($_FILES['fichierPersonnage']['tmp_name'], $this->getParameter('photo_directory') . '/import//' . 'personnage.ods');
            $aide->import_personnage();
        }


        /*return $this->render('home/test.html.twig', [
            
          
        ]);

        return $this->redirectToRoute('serie');
    }

    #[IsGranted("ROLE_admin")
      #[Route("/export", name="export")
     
    public function export(Aide $aide): Response
    {

        if (isset($_POST['series']) || (isset($_GET['type']) && $_GET['type'] == 'serie')) {
            $affiches = $aide->export_serie($_GET);
        }
        if (isset($_POST['episode']) || (isset($_GET['type']) && $_GET['type'] == 'episode')) {
            $aide->export_episode($_GET);
        }
        if (isset($_POST['acteur']) || (isset($_GET['type']) && $_GET['type'] == 'acteur')) {
            $aide->export_acteur($_GET);
        }
        if (isset($_POST['personnage']) || (isset($_GET['type']) && $_GET['type'] == 'personnage')) {
            $aide->export_personnage($_GET);
        }


        $zip = new \ZipArchive();
        $zip->open($this->getParameter('photo_directory') . '/export//' . 'Export_donnée.zip', \ZipArchive::CREATE);



        $zip->addFile($this->getParameter('photo_directory') . '/export//' . 'episode.ods', "episode.ods");
        $zip->addFile($this->getParameter('photo_directory') . '/export//' . 'acteur.ods', 'acteur.ods');
        $zip->addFile($this->getParameter('photo_directory') . '/export//' . 'personnage.ods', 'personnage.ods');


        if (file_exists($this->getParameter('photo_directory') . '/export//' . 'serie.ods')) {
            $zip->addEmptyDir("Serie");
            $zip->addFile($this->getParameter('photo_directory') . '/export//' . 'serie.ods', 'Serie/' . 'serie.ods');
            $dir = opendir($this->getParameter('photo_directory') . '/photo//');
            while ($file = readdir($dir)) {
                foreach ($affiches as $affiche) {

                    if (file_exists($this->getParameter('photo_directory') . '/photo//' . $file) && $file != '.' && $file != '..' && $file == $affiche) {

                        $zip->addFile("photo/" . $file, 'Serie/' . $file);
                    }
                }
            }

            closedir($dir);
        }

        $zip->close();

        if (file_exists($this->getParameter('photo_directory') . '/export//' . 'Export_donnée.zip')) {
            $response = new Response(file_get_contents($this->getParameter('photo_directory') . "export/" . 'Export_donnée.zip'));
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition: attachment; filename=', 'Export_donnée.zip');

            unlink($this->getParameter('photo_directory') . '/export//' . 'Export_donnée.zip');
            if (file_exists($this->getParameter('photo_directory') . "export/serie.ods")) {
                unlink($this->getParameter('photo_directory') . "export/serie.ods");
            }
            if (file_exists($this->getParameter('photo_directory') . "export/episode.ods")) {
                unlink($this->getParameter('photo_directory') . "export/episode.ods");
            }
            if (file_exists($this->getParameter('photo_directory') . "export/acteur.ods")) {
                unlink($this->getParameter('photo_directory') . "export/acteur.ods");
            }
            if (file_exists($this->getParameter('photo_directory') . "export/personnage.ods")) {
                unlink($this->getParameter('photo_directory') . "export/personnage.ods");
            }
            return $response;
        } else {
            return $this->redirectToRoute('serie');
        }
        /*dump($_GET);
        return $this->render('home/test2.html.twig', [
            
          
        ]);*/
    }

    #[Route("/menuJSON", name: "menuJSON")]
    public function menuJSON(EntityManagerInterface $entityManager): JsonResponse
    {
        if ($_GET['type'] == "episode") {
            $items = $entityManager->getRepository(Episode::class)->findAll();
        } elseif ($_GET['type'] == 'serie') {
            $items = $entityManager->getRepository(Serie::class)->findAll();
        } elseif ($_GET['type'] == 'acteur') {
            $items = $entityManager->getRepository(Acteur::class)->findAll();
        } else {
            $items = $entityManager->getRepository(Personnage::class)->findAll();
        }

        switch ($_GET['type']) {
            case 'episode':
                $items = $entityManager->getRepository(Episode::class)->findAll();
                break;
            case 'serie':
                $items = $entityManager->getRepository(Serie::class)->findAll();
                break;
            case 'acteur':
                $items = $entityManager->getRepository(Acteur::class)->findAll();
                break;
            case 'user':
                $items = $entityManager->getRepository(User::class)->findAll();
                break;
            default:
                $items = $entityManager->getRepository(Personnage::class)->findAll();
                break;
        }


        $data = [];
        foreach ($items as $unItem) {
            $data[] = $unItem->dataJson();
        }


        return new JsonResponse($data, Response::HTTP_OK);
    }
}
