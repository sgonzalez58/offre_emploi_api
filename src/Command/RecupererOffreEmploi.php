<?php

namespace App\Command;

use App\Entity\Commune;
use App\Service\OffreEmploi;
use App\Entity\OffreEmploi as offre;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecupererOffreEmploi extends Command
{
    protected static $defaultName = 'app:recuperer-offre-emploi';
    private $offreEmploi;
    private $manager;

    public function __construct(OffreEmploi $offreEmploi, ManagerRegistry $registry)
    {
        parent::__construct();
        $this->offreEmploi = $offreEmploi;
        $this->manager = $registry->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $liste_offreEmploi = $this->offreEmploi->getOffreNievre();
        foreach($liste_offreEmploi as $offre){
            if(!$this->manager->getRepository(offre::class)->findOneBy(['id_pole_emploi' => $offre->id])){
                $nouvelle_offre = new offre;
                $nouvelle_offre
                    ->setIdPoleEmploi($offre->id)
                    ->setIntitule($offre->intitule)
                    ->setDescription($offre->description)
                    ->setDateDeCreation(new \DateTime($offre->dateCreation))
                    ->setDateActualisation(new \DateTime($offre->dateActualisation));
                if(property_exists($offre->lieuTravail, 'latitude')){
                    $nouvelle_offre->setLatitude($offre->lieuTravail->latitude);
                }
                if(property_exists($offre->lieuTravail, 'longitude')){
                    $nouvelle_offre->setLongitude($offre->lieuTravail->longitude);
                }
                $nouvelle_offre->setVilleLibelle($offre->lieuTravail->libelle);
                if(property_exists($offre->lieuTravail, 'commune') && $this->manager->getRepository(Commune::class)->findOneBy(['codeInsee' => $offre->lieuTravail->commune])){
                    $nouvelle_offre->setCommune($this->manager->getRepository(Commune::class)->findOneBy(['codeInsee' => $offre->lieuTravail->commune]));
                }else{
                    $villeQuery = $this->manager->getRepository(Commune::class)->findBySlug(explode(' - ', $offre->lieuTravail->libelle)[1]);
                    if($villeQuery){
                        $nouvelle_offre->setCommune($villeQuery);
                        if(!property_exists($offre->lieuTravail, 'latitude')){
                            $nouvelle_offre->setLatitude($villeQuery->getLatitude());
                            $nouvelle_offre->setLongitude($villeQuery->getLongitude());
                        }
                    }
                }
                $nouvelle_offre
                    ->setCodeMetier($offre->romeCode)
                    ->setLibelleMetier($offre->romeLibelle)
                    ->setAppellationMetier($offre->appellationlibelle);
                if(property_exists($offre->entreprise, 'nom')){
                    $nouvelle_offre->setNomEntreprise($offre->entreprise->nom);
                }
                $nouvelle_offre
                    ->setTypeContrat($offre->typeContrat)
                    ->setTypeContratLibelle($offre->typeContratLibelle)
                    ->setNatureContrat($offre->natureContrat)
                    ->setExperienceExige($offre->experienceExige)
                    ->setExperienceLibelle($offre->experienceLibelle);
                if(property_exists($offre->salaire, 'libelle')){
                    $nouvelle_offre->setSalaire($offre->salaire->libelle);
                }
                if(property_exists($offre, 'dureeTravailLibelle')){
                    $nouvelle_offre->setDureeTravail($offre->dureeTravailLibelle);
                }
                if(property_exists($offre, 'dureeTravailLibelleConverti')){
                    $nouvelle_offre->setDureeTravailConvertie($offre->dureeTravailLibelleConverti);
                }
                $nouvelle_offre
                    ->setAlternance($offre->alternance)
                    ->setNbPostes($offre->nombrePostes)
                    ->setAccessibleTH($offre->accessibleTH);
                if(property_exists($offre, 'qualificationCode')){
                    $nouvelle_offre->setCodeQualification($offre->qualificationCode);
                }
                if(property_exists($offre, 'qualificationLibelle')){
                    $nouvelle_offre->setLibelleQualification($offre->qualificationLibelle);
                }
                if(property_exists($offre, 'secteurActivite')){
                    $nouvelle_offre->setSecteurActivite($offre->secteurActivite);
                }
                if(property_exists($offre, 'secteurActiviteLibelle')){
                    $nouvelle_offre->setSecteurActiviteLibelle($offre->secteurActiviteLibelle);
                }
                $nouvelle_offre->setOrigineOffre($offre->origineOffre->urlOrigine);
                $this->manager->persist($nouvelle_offre);
                $this->manager->flush();
                $output->writeln('Ajout de l\'offre '.$offre->id);
            }else{
                $output->writeln('Cette offre existe déjà : '.$offre->id);
            }
        }

        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}