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
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManagerInterface;


class PersonnageController extends AbstractController
{
    #[Route("/ajout/personnages", name: "ajout_personnages")]
    public function ajout_personnages(EntityManagerInterface $entityManager): Response
    {
        dump($_POST);
        for ($i = 0; $i < $_POST['maxPerso']; $i++) {
            $personnage = new Personnage();

            if ($_POST['inputNom_' . $i] != '') {
                $personnage->setNom($_POST['inputNom_' . $i]);
            }
            if ($_POST['inputActeur_' . $i] != '') {
                $repActeur = $entityManager->getRepository(Acteur::class);
                $personnage->addActeur($repActeur->findActorById($_POST['inputActeur_' . $i]));
            }
            if ($_POST['inputSerie_' . $i] != '') {
                $repSerie = $entityManager->getRepository(Serie::class);
                $personnage->addSerie($repSerie->findUneSerie($_POST['inputSerie_' . $i]));
            }



            $entityManager->persist($personnage);
            dump($personnage);
        }
        $entityManager->flush();
        return $this->redirectToRoute('gerer_personnages');
    }
    #[Route("/supprimer_personnage/{id}", name: "supprimer_personnage")]
    public function supprimer_personnage($id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $personnage = $entityManager->getRepository(Personnage::class)->findCharacterById($id);

        $id = $personnage->getActeur()->getId();
        $entityManager->remove($personnage);
        $entityManager->flush();

        if ($_GET['route'] == 'gerer_personnage') {
            return $this->redirectToRoute('gerer_personnage', array('id' => $id));
        } else {
            return $this->redirectToRoute('gerer_personnages');
        }
    }

    #[Route("/supprimer_personnages", name: "supprimer_personnages")]
    public function supprimer_personnages(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $tab = array_keys($_GET);
        dump($_GET);
        foreach ($tab as $int) {

            if ($int != "checkall" && $int != "route") {
                $personnage = $entityManager->getRepository(Personnage::class)->findCharacterById($int);
                $id = $personnage->getActeur()->getId();
                $entityManager->remove($personnage);
            }
        }
        $entityManager->flush();

        if ($_GET['route'] == 'gerer_personnage') {
            return $this->redirectToRoute('gerer_personnage', array('id' => $id));
        } else {
            return $this->redirectToRoute('gerer_personnages');
        }
    }

    #[Route("/gerer_personnage/{id}", name: "gerer_personnage")]
    public function gerer_personnage(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $rep = $entityManager->getRepository(Personnage::class);
        $personnages = $rep->findCharacterByActorId($id);


        $series = $entityManager->getRepository(Serie::class)->findAll();


        $personnage = new Personnage();
        if (isset($_POST['ID'])) {
            $searchPersonnage = $rep->findCharacterById($_POST['ID']);

            if ($searchPersonnage != null) {
                $personnage = $searchPersonnage;
            }
        }

        $form = $this->createForm(PersonnageFormType::class, $personnage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $personnage->setActeur($entityManager->getRepository(Acteur::class)->findActorById($id));

            $entityManager->persist($personnage);
            $entityManager->flush();

            return $this->redirectToRoute('gerer_personnage', array('id' => $id));
        }

        return $this->render('personnage/gerer_personnage.html.twig', [
            'personnages' => $personnages,
            'formPersonnage' => $form->createView(),
            "id" => $id,
            'route' => 'gerer_personnage',
            'series' => $series
        ]);
    }
    #[Route("/gerer_personnages", name: "gerer_personnages")]
    public function gerer_personnages(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $rep = $entityManager->getRepository(Personnage::class);
        $personnages = $rep->findAll();
        $acteurs = $entityManager->getRepository(Acteur::class)->findAll();
        $series = $entityManager->getRepository(Serie::class)->findAll();

        $personnage = new Personnage();
        if (isset($_POST['ID'])) {
            $searchPersonnage = $rep->findCharacterById($_POST['ID']);

            if ($searchPersonnage != null) {
                $personnage = $searchPersonnage;
            }
        }

        $form = $this->createForm(PersonnageFormType::class, $personnage);
        $form->handleRequest($request);
        $error = ' ';
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($personnage);
            $entityManager->flush();
            return $this->redirectToRoute('gerer_personnages');
        }

        return $this->render('personnage/gerer_personnage.html.twig', [
            'personnages' => $personnages,
            'formPersonnage' => $form->createView(),
            'route' => 'gerer_personnages',
            'acteurs' => $acteurs,
            'series' => $series

        ]);
    }
}
