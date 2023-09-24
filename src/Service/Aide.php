<?php

namespace App\Service;

use App\Entity\Series;
use App\Entity\Saison;
use App\Entity\Acteur;
use App\Entity\Actor;
use App\Entity\Character;
use App\Entity\Personnage;
use App\Entity\Episode;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as Write;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use Doctrine\ORM\EntityManagerInterface;



class Aide extends AbstractController
{
    public function import_serie(EntityManagerInterface $entityManager)
    {
        $repSerie = $entityManager->getRepository(Series::class);
        $repSaison = $entityManager->getRepository(Saison::class);

        $reader = new Ods();

        $reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($this->getParameter('photo_directory') . 'import/serie.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow();
        $maxCol = $worksheet->getHighestColumn();
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);


        $titreCol = array();

        for ($ligne = 1; $ligne <= $maxLigne; $ligne++) {
            $serie = new Series();
            for ($col = 1; $col <= $maxColId; $col++) {
                $value = $worksheet->getCell($col, $ligne)->getValue();

                if ($value != null) {

                    if ($ligne == 1) {
                        $titreCol[$value] = $col;
                    } else {

                        if ($col == $titreCol['Nom']) {
                            $serie->setName($value);
                        }
                        if ($col == $titreCol['Résumé']) {
                            $serie->setSummary($value);
                        }
                        if ($col == $titreCol['Date']) {
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);

                            $serie->setReleaseDate($date);
                        }
                        if ($col == $titreCol['Saison']) {

                            for ($i = 1; $i <= intval($value); $i++) {
                                $saison = new Season();
                                $saison->setNumero(1);
                                $saison->setNumero($i);
                                $serie->addSeason($saison);
                                $entityManager->persist($saison);
                            }
                        }
                        if ($col == $titreCol['Affiche']) {
                            $serie->setPoster($value);
                        }
                        if ($col == $titreCol['URL Bande Annonce']) {
                            $serie->setTrailerUrl($value);
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

    public function import_acteur(EntityManagerInterface $entityManager)
    {
        $repActeur = $entityManager->getRepository(Acteur::class);

        $reader = new Ods();

        $reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($this->getParameter('photo_directory') . 'import/acteur.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow();
        $maxCol = $worksheet->getHighestColumn();
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);


        $titreCol = array();

        for ($ligne = 1; $ligne <= $maxLigne; $ligne++) {
            $acteur = new Actor();
            for ($col = 1; $col <= $maxColId; $col++) {
                $value = $worksheet->getCell($col, $ligne)->getValue();

                if ($value != null) {

                    if ($ligne == 1) {
                        $titreCol[$value] = $col;
                    } else {

                        if ($col == $titreCol['Nom']) {
                            $acteur->setLastname($value);
                        }
                        if ($col == $titreCol['Prénom']) {
                            $acteur->setFirstname($value);
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

    public function import_episode(EntityManagerInterface $entityManager)
    {
        $repEp = $entityManager->getRepository(Episode::class);
        $repSerie = $entityManager->getRepository(Serie::class);
        $repSaison = $entityManager->getRepository(Saison::class);



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
                $value = $worksheet->getCell($col, $ligne)->getValue();

                if ($value != null) {

                    if ($ligne == 1) {
                        $titreCol[$value] = $col;
                    } else {

                        if ($col == $titreCol['Nom épisode']) {
                            $episode->setName($value);
                        }
                        if ($col == $titreCol['Résumé épisode']) {
                            $episode->setResume($value);
                        }
                        if ($col == $titreCol['Date ep']) {
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                            $episode->setRealeseDate($date);
                        }

                        if ($col == $titreCol['nom série']) {
                            $serie = $repSerie->findUneSerieByName($value);
                            if ($serie == null) {
                                $serieNew = new Series();
                                $serieNew->setName($value);

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
                                $saison = new Season();
                                $saison->setNumero(1);
                                $saison->setSeries($serie);
                            }
                            $entityManager->persist($saison);
                            $episode->setSeason($saison);
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

    public function import_personnage(EntityManagerInterface $entityManager)
    {
        $reader = new Ods();

        $reader->setReadDataOnly(TRUE);

        $spreadsheet = $reader->load($this->getParameter('photo_directory') . 'import/personnage.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow();
        $maxCol = $worksheet->getHighestColumn();
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);


        $titreCol = array();

        for ($ligne = 1; $ligne <= $maxLigne; $ligne++) {
            $personnage = new Character();
            for ($col = 1; $col <= $maxColId; $col++) {
                $value = $worksheet->getCell($col, $ligne)->getValue();

                if ($value != null) {

                    if ($ligne == 1) {
                        $titreCol[$value] = $col;
                    } else {

                        if ($col == $titreCol['Nom']) {
                            $personnage->setName($value);
                        }
                        if ($col == $titreCol['Acteur']) {
                            $array_acteur = explode(' ', $value);
                            $acteur = $entityManager->getRepository(Acteur::class)->findActorByIdAndName($array_acteur[0], $array_acteur[1]);
                            if ($acteur == null) {
                                $acteur = new Actor();
                                $acteur->setLastname($array_acteur[1]);
                                $acteur->setFirstname($array_acteur[0]);
                                $entityManager->persist($acteur);
                                $entityManager->flush();
                            }
                            $personnage->addActor($acteur);
                        }
                        if ($col == $titreCol['Série']) {
                            $serie = $entityManager->getRepository(Serie::class)->findUneSerieByName($value);
                            if ($serie == null) {
                                $serie = new Series();
                                $serie->setName($value);
                                $entityManager->persist($serie);
                                $entityManager->flush();
                            }
                            $personnage->addSeries($serie);
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

    public function export_serie($get, EntityManagerInterface $entityManager)
    {

        $spread = new SpreadSheet();
        $writer = new Write($spread);

        $data = [['Nom', 'Résumé', 'Date', 'Saison', 'Affiche', 'URL Bande Annonce']];
        if ($get == []) {
            $series = $entityManager->getRepository(Series::class)->findAll();
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

    public function export_episode($get, $entityManager)
    {
        $repEpisode = $entityManager->getRepository(Episode::class);
        



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
    public function export_personnage($get, EntityManagerInterface $entityManager)
    {
        $repPerso = $entityManager->getRepository(Personnage::class);


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

    public function export_acteur($get, EntityManagerInterface $entityManager)
    {
        $repActeur = $entityManager->getRepository(Acteur::class);

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
