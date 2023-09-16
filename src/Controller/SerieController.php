<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Serie;
use App\Entity\Acteur;
use App\Entity\Character;
use App\Entity\Personnage;
use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Form\SerieFormType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Aide;
use Doctrine\ORM\EntityManagerInterface;


class SerieController extends AbstractController
{
    #[Route("/serie", name: "serie")]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $rep = $entityManager->getRepository(Serie::class);
        $lesSerie = $rep->findAll();

        foreach ($lesSerie as $serie) {
            $summary = $serie->getResume();
            $summaryM = substr($summary, 0, 150) . '...';
            $serie->setResume($summaryM);
        }

        return $this->render('serie/index.html.twig', [
            'serie' => $lesSerie,

        ]);
    }

    #[Route("/ajout/series", name: "ajout_series")]
    public function ajout_series(EntityManagerInterface $entityManager): Response
    {

        for ($i = 0; $i < $_POST['maxSerie']; $i++) {
            $serie = new Series();

            if ($_POST['inputNom_' . $i] != '') {
                $serie->setName($_POST['inputNom_' . $i]);
            }
            if ($_POST['inputResume_' . $i] != '') {
                $serie->setResume($_POST['inputResume_' . $i]);
            }
            if ($_POST['inputDate_' . $i] != '') {
                $date = new \DateTime($_POST['inputDate_' . $i]);

                $serie->setDateDiff($date);
            }
            if ($_POST['inputSaison_' . $i] != '') {

                for ($sa = 0; $sa < $_POST['inputSaison_' . $i]; $sa++) {
                    $saison = new Season();
                    $saison->setNumero($sa + 1);

                    $entityManager->persist($saison);
                    $serie->addSeason($saison);
                }
            }
            if ($_FILES['inputFile_' . $i]['name'] != '') {
                move_uploaded_file($_FILES['inputFile_' . $i]['tmp_name'], $this->getParameter('photo_directory') . '/photo/' . $_FILES['inputFile_' . $i]['name']);
                $serie->setPoster($_FILES['inputFile_' . $i]['name']);
            }
            if ($_POST['nbPerso_' . $i] != '') {
                for ($p = 0; $p < $_POST['nbPerso_' . $i]; $p++) {

                    $perso = $entityManager->getRepository(Personnage::class)->findCharacterByActorIdAndName($_POST['persoNom_' . $i . '_' . $p], $_POST['acteur_' . $i . '_' . $p]);
                    dump($perso);
                    if ($perso == null) {
                        $perso = new Character();
                        $perso->addActor($entityManager->getRepository(Acteur::class)->findActorById($_POST['acteur_' . $i . '_' . $p]));
                        $perso->addSeries($serie);
                        $entityManager->persist($saison);
                    }
                }
            }
            if ($_POST['inputURL_' . $i] != '') {
                $serie->setUrlBa($_POST['inputURL_' . $i]);
            }
            $entityManager->persist($serie);
        }
        $entityManager->flush();
        return $this->redirectToRoute('gerer_serie');
    }

    #[Route("/detail_serie/{id}", name: "detail_serie")]
    public function detail_serie($id, EntityManagerInterface $entityManager): Response
    {
        $rep = $entityManager->getRepository(Serie::class);
        $serie = $rep->findUneSerie($id);
        $url = $serie->getUrlBa();
        $ch_url1 = substr($url, 0, 13);
        $ch_url3 = substr($url, 16);
        $embed_url = $ch_url1 . "be.com" . "/embed" . $ch_url3;


        return $this->render('serie/detail_serie.html.twig', [
            'serie' => $serie,
            'url' => $embed_url,
            'youtube' => $ch_url1
        ]);
    }

    #[Route("/gerer_serie", name: "gerer_serie")]
    public function gererserie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $rep = $entityManager->getRepository(Serie::class);
        $lesSerie = $rep->findAll();

        $serie = new Series();

        if (isset($_POST['ID'])) {
            $searchSerie = $rep->findUneSerie($_POST['ID']);
            if ($searchSerie != null) {
                $serie = $searchSerie;
            }
        }

        $acteurs = $entityManager->getRepository(Acteur::class)->findAll();
        $form = $this->createForm(SerieFormType::class, $serie);
        $form->handleRequest($request);
        $error = ' ';

        if ($form->isSubmitted() && $form->isValid()) {
            $dumpPhoto = $form->get('photo')->getData();

            if ($form->get('photo')->getData() != null) {
                $images = $form->get('photo')->getData();
                $imgExt = $images->guessClientExtension();
                $ext = array('png', 'jpeg', 'jpg', 'gif', 'svg');
                $fileName = $images->getClientOriginalName();
                dump(in_array($imgExt, $ext, $strict = false));
                if (in_array($imgExt, $ext, $strict = false)) {
                    $images->move($this->getParameter('photo_directory') . '/photo', $fileName);
                }
                $serie->setAffiche($fileName);
            }
            $entityManager->persist($serie);

            for ($i = 0; $i < count($_POST) - 2; $i++) {
                if (isset($_POST["acteur_" . $i]) && isset($_POST['persoNom_' . $i])) {
                    if ($_POST['acteur_' . $i] != '' && $_POST['persoNom_' . $i] != '') {

                        $acteur = $entityManager->getRepository(Acteur::class)->findActorById($_POST['acteur_' . $i]);

                        $personnage = new Character();
                        $personnage->addActor($acteur)->addSeries($serie)->setName($_POST['persoNom_' . $i]);

                        $entityManager->persist($personnage);
                    }
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('gerer_serie');
        }



        foreach ($lesSerie as $uneSerie) {

            $uneSerie->setResume(str_replace("'", "\'", $uneSerie->getResume()));
            $uneSerie->setResume(str_replace("\r\n", " ", $uneSerie->getResume()));
        }
        return $this->render('serie/gerer_serie.html.twig', [
            'serie' => $lesSerie,
            "formSerie" => $form->createView(),
            'error' => $error,
            'titre' => 'Nouvelle série',
            'acteurs' => $acteurs

        ]);
    }



    #[Route("/supprimer_serie/{id}", name: "supprimer_serie")]
    public function supprimer_serie($id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $serie = $entityManager->getRepository(Serie::class)->findUneSerie($id);

        $entityManager->remove($serie);
        $entityManager->flush();

        return $this->redirectToRoute('gerer_serie');
    }

    #[Route("/supprimer_series", name: "supprimer_series")]
    public function supprimer_series(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $tab = array_keys($_GET);

        foreach ($tab as $int) {

            if ($int != "checkall") {
                $serie = $entityManager->getRepository(Serie::class)->findUneSerie($int);
                if (file_exists($serie->getAffiche())) {
                    unlink($this->getParameter('photo_directory') . '/photo//' . $serie->getAffiche());
                }

                $entityManager->remove($serie);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('gerer_serie');
    }





    #[Route("/test", name: "test")]
    public function test(Request $request, Aide $aide, EntityManagerInterface $entityManager): Response
    {

        dump($_GET, $_POST);

        $series = $entityManager->getRepository(Serie::class)->findAll();
        $acteurs = $entityManager->getRepository(Acteur::class)->findAll();
        //dump($series[0]->dataJson());
        return $this->render('home/test.html.twig', [
            'acteurs' => $acteurs,
            "series" => $series
        ]);
    }
    /**
     * @Route("/test2", name="test2")
     */
    public function test2(Request $request, EntityManagerInterface $entityManager): Response
    {



        $episode = $entityManager->getRepository(Episode::class)->findAll();


        return $this->render('home/test2.html.twig', [
            'episodes' => $episode
        ]);
    }
}
