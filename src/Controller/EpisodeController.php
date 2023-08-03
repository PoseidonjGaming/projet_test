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
use App\Service\Aide;
use Doctrine\ORM\EntityManagerInterface;

class EpisodeController extends AbstractController
{
    #[Route("/ajout/episodes", name: "ajout_episodes")]
    public function ajout_episodes(EntityManagerInterface $entityManager): Response
    {
        dump($_POST);

        for ($i = 0; $i < $_POST['maxEpisode']; $i++) {
            $episode = new Episode();

            if ($_POST['inputNom_' . $i] != '') {
                $episode->setNom($_POST['inputNom_' . $i]);
            }
            if ($_POST['inputResume_' . $i] != '') {
                $episode->setResume($_POST['inputResume_' . $i]);
            } elseif ($_POST['inputResume_' . $i] == null) {
                $episode->setResume("résumé manquant");
            }
            if ($_POST['inputDate_' . $i] != '') {
                $date = new \DateTime($_POST['inputDate_' . $i]);

                $episode->setDatePremDiff($date);
            }
            if ($_POST['inputSerie_' . $i] != '') {
                $serie = $entityManager->getRepository(Serie::class)->findUneSerie($_POST['inputSerie_' . $i]);
            }
            if ($_POST['inputSaison_' . $i] != '') {
                $saison = $entityManager->getRepository(Saison::class)->findUneSaisonByNum($_POST['inputSaison_' . $i], $serie->getId());
                if ($saison == null) {
                    $saison = new Saison();
                    $saison->setSerie($serie);
                    $saison->setNumero($_POST['inputSaison_' . $i]);
                    $entityManager->persist($saison);
                    $entityManager->flush();
                } else {
                    $saison->setNbEpisode($saison->getNbEpisode() + 1);
                }

                $episode->setSaison($saison);
            }

            $entityManager->persist($episode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gerer_episodes');
    }

    #[Route("/supprimer_episode/{id}", name: "supprimer_episode")]
    public function supprimer_episode($id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $episode = $entityManager->getRepository(Episode::class)->findUnEpisode($id);
        $saisonId = $episode->getSaison()->getId();

        $entityManager->remove($episode);
        $entityManager->flush();

        return $this->redirectToRoute('gerer_episode', array('id' => $saisonId));
    }

    #[Route("/supprimer_episodes", name: "supprimer_episodes")]
    public function supprimer_episodes(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');

        $tab = array_keys($_GET);
        dump($_GET);
        $exclude = ["titre", "dateStart", "dateEnd", "saisonFiltre", "checkExport", "type", "checkall"];
        foreach ($tab as $int) {

            if (!in_array($int, $exclude)) {
                $episode = $entityManager->getRepository(Episode::class)->findUnEpisode($int);
                $id = $episode->getSaison()->getId();
                $entityManager->remove($episode);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('gerer_episodes');
    }

    #[Route("/gerer_episode/{id}", name: "gerer_episode")]
    public function gerer_episode(Request $request, $id, Aide $aide, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $rep = $entityManager->getRepository(Episode::class);
        $ListeEpisodes = $rep->findEpisodes($id);


        $episode = new Episode();
        if (isset($_POST['ID'])) {
            $searchEpisode = $rep->findUnEpisode($_POST['ID']);

            if ($searchEpisode != null) {
                $episode = $searchEpisode;
            }
        }

        $form = $this->createForm(EpisodeFormType::class, $episode);
        $form->handleRequest($request);
        $error = ' ';

        if ($form->isSubmitted() && $form->isValid()) {


            $repSaison = $entityManager->getRepository(Saison::class);
            $saison = $repSaison->findUneSaison($id);
            $serie = $saison->getSerie();

            $episode->setSaison($saison);

            if ($episode->getId() == null && !isset($_POST['last_season']) && (count($saison->getEpisodes()) + 1) > intval($saison->getNbEpisode())) {
                $saison->setNbEpisode($saison->getNbEpisode() + 1);
            } elseif (isset($_POST['last_season'])) {
                $saisonNext = new Saison();
                $saisonNext->setSerie($saison->getSerie());
                $entityManager->persist($saisonNext);
            }


            $entityManager->persist($episode);

            $entityManager->flush();

            return $this->redirectToRoute('gerer_episode', array('id' => $id));
        }


        foreach ($ListeEpisodes as $UnEpisode) {

            $UnEpisode->setResume(str_replace("'", "\'", $UnEpisode->getResume()));
            $UnEpisode->setResume(str_replace("\r\n", " ", $UnEpisode->getResume()));
        }

        dump($ListeEpisodes);
        return $this->render('episode/gerer_episode.html.twig', [

            'episodes' => $ListeEpisodes,
            'formEpisode' => $form->createView(),
            'id' => $id

        ]);
    }

    #[Route("/gerer_episodes", name: "gerer_episodes")]
    public function gerer_episodes(Request $request, Aide $aide, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');

        $rep = $entityManager->getRepository(Episode::class);
        $ListeEpisodes = $rep->findAll();
        $series = $entityManager->getRepository(Serie::class)->findAll();
        $episode = new Episode();
        if (isset($_POST['ID'])) {
            $searchEpisode = $rep->findUnEpisode($_POST['ID']);

            if ($searchEpisode != null) {
                $episode = $searchEpisode;
            }
        }

        $form = $this->createForm(EpisodeFormType::class, $episode);
        $form->handleRequest($request);
        $error = ' ';

        if ($form->isSubmitted() && $form->isValid()) {

            $repSaison = $entityManager->getRepository(Saison::class);
            $saison = $repSaison->findUneSaisonByNum($_POST['saison'], $_POST['serie']);


            if ($saison == null) {
                $saisonNext = new Saison();
                $saisonNext->setNumero(count($entityManager->getRepository(Serie::class)->findUneSerie($_POST['serie'])->getSaisons()) + 1);
                $saisonNext->setSerie($entityManager->getRepository(Serie::class)->findUneSerie($_POST['serie']));
                $entityManager->persist($saisonNext);
                $episode->setSaison($saisonNext);
            } elseif ($episode->getId() == null && !isset($_POST['last_season']) && (count($saison->getEpisodes()) + 1) > intval($saison->getNbEpisode())) {
                $saison->setNbEpisode($saison->getNbEpisode() + 1);

                $episode->setSaison($saison);
            } elseif (isset($_POST['last_season'])) {
                $saisonNext = new Saison();
                $saisonNext->setSerie($saison->getSerie());
                $entityManager->persist($saisonNext);
                $episode->setSaison($saisonNext);
            }


            $entityManager->persist($episode);

            $entityManager->flush();


            return $this->redirectToRoute('gerer_episodes');
        }


        foreach ($ListeEpisodes as $UnEpisode) {

            $UnEpisode->setResume(str_replace("'", "\'", $UnEpisode->getResume()));
            $UnEpisode->setResume(str_replace("\r\n", " ", $UnEpisode->getResume()));
        }

        return $this->render('episode/gerer_episode.html.twig', [

            'episodes' => $ListeEpisodes,
            'series' => $series,
            'formEpisode' => $form->createView(),


        ]);
    }
}
