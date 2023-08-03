<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Serie;
use App\Entity\Saison;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\SaisonFormType;
use Doctrine\ORM\EntityManagerInterface;

class SaisonController extends AbstractController
{
    #[Route("/supprimer_saison/{id}", name: "supprimer_saison")]
    public function supprimer_saison($id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $saison = $entityManager->getRepository(Saison::class)->findUneSaison($id);
        $id = $saison->getSerie()->getId();

        $entityManager->remove($saison);
        $entityManager->flush();

        return $this->redirectToRoute('gerer_saison', array('id' => $id));
    }

    #[Route("/supprimer_saisons", name: "supprimer_saisons")]
    public function supprimer_saisons(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $tab = array_keys($_GET);


        foreach ($tab as $int) {
            if ($int != "checkall") {

                $saison = $entityManager->getRepository(saison::class)->findUneSaison($int);
                $id = $saison->getSerie()->getId();

                $entityManager->remove($saison);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('gerer_saison', array('id' => $id));
    }

    #[Route("/gerer_saison/{id}", name: "gerer_saison")]
    public function gerer_saison(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_admin');
        $rep = $entityManager->getRepository(Saison::class);
        $saisons = $rep->findSaison($id);
        $saison = new Saison();
        if (isset($_POST['ID'])) {
            $searchSaison = $rep->findUneSaison($_POST['ID']);

            if ($searchSaison != null) {
                $saison = $searchSaison;
            }
        }
        $form = $this->createForm(SaisonFormType::class, $saison);
        $form->handleRequest($request);
        $error = ' ';

        if ($form->isSubmitted() && $form->isValid()) {


            $repSerie = $entityManager->getRepository(Serie::class);
            $serie = $repSerie->findUneSerie($id);


            if ($saisons == null) {
                $saison->setNumero(1);
            } else if ($searchSaison == null) {
                $saison->setNumero($saisons[0]->getNumero() + 1);
            }
            $saison->setSerie($serie);

            $entityManager->persist($saison);
            $entityManager->flush();


            return $this->redirectToRoute('gerer_saison', array('id' => $id));
        }

        return $this->render('saison/gerer_saison.html.twig', [

            'formSaison' => $form->createView(),
            'saisons' => $saisons,
            'id' => $id

        ]);
    }
}
