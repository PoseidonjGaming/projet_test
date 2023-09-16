<?php

namespace App\Service;

use App\Entity\Serie;
use App\Entity\Saison;
use App\Entity\Acteur;
use App\Entity\Personnage;
use App\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as Write;
use PhpOffice\PhpSpreadsheet\Reader\Ods;



class Aide extends AbstractController
{
    public function import_serie()
    {
        $repSerie = $entityManager->getRepository(Serie::class);
        $repSaison = $entityManager->getRepository(Saison::class);
        $entityManager = $this->getDoctrine()->getManager();

        $reader = new Ods();

        $reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($this->getParameter('photo_directory') . 'import/serie.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow();
        $maxCol = $worksheet->getHighestColumn();
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);


        $titreCol = array();

        for ($ligne = 1; $ligne <= $maxLigne; $ligne++) {
            $serie = new Serie();
            for ($col = 1; $col <= $maxColId; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $ligne)->getValue();

                if ($value != null) {

                    if ($ligne == 1) {
                        $titreCol[$value] = $col;
                    } else {

                        if ($col == $titreCol['Nom']) {
                            $serie->setNom($value);
                        }
                        if ($col == $titreCol['Résumé']) {
                            $serie->setResume($value);
                        }
                        if ($col == $titreCol['Date']) {
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);

                            $serie->setDateDiff($date);
                        }
                        if ($col == $titreCol['Saison']) {

                            for ($i = 1; $i <= intval($value); $i++) {
                                $saison = new Saison();
                                $saison->setNbEpisode(1);
                                $saison->setNumero($i);
                                $serie->addSaison($saison);
                                $entityManager->persist($saison);
                            }
                        }
                        if ($col == $titreCol['Affiche']) {
                            $serie->setAffiche($value);
                        }
                        if ($col == $titreCol['URL Bande Annonce']) {
                            $serie->setUrlBa($value);
                        }

                        if ($col == $maxColId) {
                            $entityManager->persist($serie);
                            $entityManager->flush();
                        }
                    }
                }
            }
        }
        unlink($this->getParameter('photo_directory') . 'import/serie.ods');
    }

    public function import_acteur()
    {
        $repActeur = $entityManager->getRepository(Acteur::class);
        $entityManager = $this->getDoctrine()->getManager();

        $reader = new Ods();

        $reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($this->getParameter('photo_directory') . 'import/acteur.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow();
        $maxCol = $worksheet->getHighestColumn();
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);


        $titreCol = array();

        for ($ligne = 1; $ligne <= $maxLigne; $ligne++) {
            $acteur = new Acteur();
            for ($col = 1; $col <= $maxColId; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $ligne)->getValue();

                if ($value != null) {

                    if ($ligne == 1) {
                        $titreCol[$value] = $col;
                    } else {

                        if ($col == $titreCol['Nom']) {
                            $acteur->setNom($value);
                        }
                        if ($col == $titreCol['Prénom']) {
                            $acteur->setPrenom($value);
                        }
                        if ($col == $maxColId) {
                            $entityManager->persist($acteur);
                            $entityManager->flush();
                        }
                    }
                }
            }
        }
        unlink($this->getParameter('photo_directory') . 'import/acteur.ods');
    }

    public function import_episode()
    {
        $repEp = $entityManager->getRepository(Episode::class);
        $repSerie = $entityManager->getRepository(Serie::class);
        $repSaison = $entityManager->getRepository(Saison::class);
        $entityManager = $this->getDoctrine()->getManager();



        $reader = new Ods();
        $reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($this->getParameter('photo_directory') . 'import/episode.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow();
        $maxCol = $worksheet->getHighestColumn();
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);

        $titreCol = array();

        for ($ligne = 1; $ligne <= $maxLigne; $ligne++) {
            $episode = new Episode();
            for ($col = 1; $col <= $maxColId; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $ligne)->getValue();

                if ($value != null) {

                    if ($ligne == 1) {
                        $titreCol[$value] = $col;
                    } else {

                        if ($col == $titreCol['Nom épisode']) {
                            $episode->setNom($value);
                        }
                        if ($col == $titreCol['Résumé épisode']) {
                            $episode->setResume($value);
                        }
                        if ($col == $titreCol['Date ep']) {
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                            $episode->setDatePremDiff($date);
                        }

                        if ($col == $titreCol['nom série']) {
                            $serie = $repSerie->findUneSerieByName($value);
                            if ($serie == null) {
                                $serieNew = new Serie();
                                $serieNew->setNom($value);

                                $entityManager->persist($serieNew);
                            }
                        }
                        if ($col == $titreCol['saison']) {
                            $saison = $repSaison->findUneSaisonByNum($value, $serie->getId());
                            if ($saison != null) {
                                if (count($saison->getNbEpisode()) == $saison->getNbEpisode()) {
                                    $saison->setNbEpisode($saison->getNbEpisode() + 1);
                                }
                            } else {
                                $saison = new Saison();
                                $saison->setNumero(1);
                                $saison->setNbEpisode(1);
                                $saison->setSerie($serie);
                            }
                            $entityManager->persist($saison);
                            $episode->setSaison($saison);
                        }

                        if ($col == $maxColId) {
                            $entityManager->persist($episode);
                            $entityManager->flush();
                        }
                    }
                }
            }
        }
        unlink($this->getParameter('photo_directory') . 'import/episode.ods');
    }

    public function import_personnage()
    {
        $entityManager = $this->getDoctrine()->getManager();





        $reader = new Ods();

        $reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($this->getParameter('photo_directory') . 'import/personnage.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow();
        $maxCol = $worksheet->getHighestColumn();
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);


        $titreCol = array();

        for ($ligne = 1; $ligne <= $maxLigne; $ligne++) {
            $personnage = new Personnage();
            for ($col = 1; $col <= $maxColId; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $ligne)->getValue();

                if ($value != null) {

                    if ($ligne == 1) {
                        $titreCol[$value] = $col;
                    } else {

                        if ($col == $titreCol['Nom']) {
                            $personnage->setNom($value);
                        }
                        if ($col == $titreCol['Acteur']) {
                            $array_acteur = explode(' ', $value);
                            $acteur = $entityManager->getRepository(Acteur::class)->findActorByIdAndName($array_acteur[0], $array_acteur[1]);
                            if ($acteur == null) {
                                $acteur = new Acteur();
                                $acteur->setNom($array_acteur[1]);
                                $acteur->setPrenom($array_acteur[0]);
                                $entityManager->persist($acteur);
                                $entityManager->flush();
                            }
                            $personnage->setActeur($acteur);
                        }
                        if ($col == $titreCol['Série']) {
                            $serie = $entityManager->getRepository(Serie::class)->findUneSerieByName($value);
                            if ($serie == null) {
                                $serie = new Serie();
                                $serie->setNom($value);
                                $entityManager->persist($serie);
                                $entityManager->flush();
                            }
                            $personnage->setSerie($serie);
                        }


                        if ($col == $maxColId) {
                            $entityManager->persist($personnage);
                            $entityManager->flush();
                        }
                    }
                }
            }
        }
        unlink($this->getParameter('photo_directory') . 'import/personnage.ods');
    }

    public function export_serie($get)
    {

        $repSerie = $entityManager->getRepository(Serie::class);
        $entityManager = $this->getDoctrine()->getManager();




        $spread = new SpreadSheet();
        $writer = new Write($spread);

        $data = [['Nom', 'Résumé', 'Date', 'Saison', 'Affiche', 'URL Bande Annonce']];
        if ($get == []) {
            $series = $repSerie->findAll();
        } elseif ($get != []) {
            $tab = explode(',', $get['listeExport']);
            $series = [];
            foreach ($tab as $int) {
                $serie = $entityManager->getRepository(Serie::class)->findUneSerie($int);
                array_push($series, $serie);
            }
        } else {
            $series = null;
        }
        $affiches = [];
        if ($series != null) {

            foreach ($series as $uneSerie) {

                $serie = [$uneSerie->getNom(), $uneSerie->getResume(), \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($uneSerie->getDateDiff()->format('d/m/Y')), count($uneSerie->getSaisons()), $uneSerie->getAffiche(), $uneSerie->getUrlBa()];

                array_push($data, $serie);
                array_push($affiches, $uneSerie->getAffiche());
            }
            $spread->getActiveSheet()->fromArray($data, NULL, 'A1');

            $writer->save($this->getParameter('photo_directory') . 'export/serie.ods');

            return $affiches;
        }
    }

    public function export_episode($get)
    {
        $repEpisode = $entityManager->getRepository(Episode::class);
        $entityManager = $this->getDoctrine()->getManager();



        $spread = new SpreadSheet();
        $writer = new Write($spread);

        $data = [['Nom épisode', 'Résumé épisode', 'Date ep', 'nom série', 'saison']];


        if ($get == []) {
            $episodes = $repEpisode->findAll();
        } elseif ($get != []) {
            $tab = explode(',', $get['listeExport']);
            $episodes = [];
            foreach ($tab as $int) {
                $episode = $entityManager->getRepository(Episode::class)->findEpisodeById($int);
                array_push($episodes, $episode);
            }
        } else {
            $episodes = null;
        }

        if ($episodes != null) {
            foreach ($episodes as $unEpisode) {

                $episode = [$unEpisode->getNom(), $unEpisode->getResume(), \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($unEpisode->getDatePremDiff()->format('d/m/Y')), $unEpisode->getSaison()->getSerie()->getNom(), $unEpisode->getSaison()->getNumero()];


                array_push($data, $episode);
            }
            $spread->getActiveSheet()->fromArray($data, NULL, 'A1');


            $writer->save($this->getParameter('photo_directory') . 'export/episode.ods');
        }
    }
    public function export_personnage($get)
    {
        $repPerso = $entityManager->getRepository(Personnage::class);
        $entityManager = $this->getDoctrine()->getManager();


        $spread = new SpreadSheet();
        $writer = new Write($spread);

        $data = [['Nom', 'Série', 'Acteur']];

        if ($get == []) {
            $personnages = $repPerso->findAll();
        } elseif ($get != []) {
            $tab = explode(',', $get['listeExport']);
            $personnages = [];
            foreach ($tab as $int) {
                $personnage = $entityManager->getRepository(Personnage::class)->findCharacterById($int);
                array_push($personnages, $personnage);
            }
        } else {
            $personnages = null;
        }
        if ($personnages != null) {
            foreach ($personnages as $unPersonnage) {

                $serie = [$unPersonnage->getNom(), $unPersonnage->getSerie()->getNom(), $unPersonnage->getActeur()->getPrenom() . ' ' . $unPersonnage->getActeur()->getNom()];


                array_push($data, $serie);
            }
            $spread->getActiveSheet()->fromArray($data, NULL, 'A1');




            $writer->save($this->getParameter('photo_directory') . 'export/personnage.ods');
        }
    }

    public function export_acteur($get)
    {
        $repActeur = $entityManager->getRepository(Acteur::class);
        $entityManager = $this->getDoctrine()->getManager();

        $spread = new SpreadSheet();
        $writer = new Write($spread);

        $data = [['Nom', 'Prénom']];
        if ($get == []) {
            $acteurs = $repActeur->findAll();
        } elseif ($_GET != []) {
            $tab = explode(',', $get['listeExport']);
            $acteurs = [];
            foreach ($tab as $int) {


                $acteur = $entityManager->getRepository(Acteur::class)->findActorById($int);
                array_push($acteurs, $acteur);
            }
        } else {
            $acteurs = null;
        }

        if ($acteurs != null) {
            foreach ($acteurs as $unActeur) {

                $acteur = [$unActeur->getNom(), $unActeur->getPrenom()];
                array_push($data, $acteur);
            }

            $spread->getActiveSheet()->fromArray($data, NULL, 'A1');

            $writer->save($this->getParameter('photo_directory') . 'export/acteur.ods');
        }
    }
}
