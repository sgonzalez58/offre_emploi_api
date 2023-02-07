<?php

namespace App\Command;

use App\Entity\Commune;
use App\Service\OffreEmploi as serviceOffre;
use App\Entity\OffreEmploi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecupererOffreEmploi extends Command
{
    protected static $defaultName = 'app:recuperer-offre-emploi';
    private $offreEmploi;
    private $em;

    public function __construct(serviceOffre $offreEmploi, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->offreEmploi = $offreEmploi;
        $this->em = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach($this->em->getRepository(OffreEmploi::class)->findAll() as $offre){
            if($offre->getIdPoleEmploi()){
                $output->writeln('Suppression offre n°: '.$offre->getId());
                $this->em->remove($offre);
            }
        }
        $this->em->flush();
        $liste_offreEmploi = $this->offreEmploi->getOffreNievre();
        foreach($liste_offreEmploi as $offre){
            if(!$this->em->getRepository(OffreEmploi::class)->findOneBy(['id_pole_emploi' => $offre->id])){
                $nouvelle_offre = new OffreEmploi;
                $nouvelle_offre
                    ->setIdPoleEmploi($offre->id)
                    ->setIntitule($offre->intitule)
                    ->setDateDeCreation(new \DateTime($offre->dateCreation))
                    ->setDateActualisation(new \DateTime($offre->dateActualisation));
                if(property_exists($offre, 'description')){
                    $nouvelle_offre->setDescription($offre->description);
                }
                if(property_exists($offre->lieuTravail, 'latitude')){
                    $nouvelle_offre->setLatitude($offre->lieuTravail->latitude);
                }
                if(property_exists($offre->lieuTravail, 'longitude')){
                    $nouvelle_offre->setLongitude($offre->lieuTravail->longitude);
                }
                $nouvelle_offre->setVilleLibelle($offre->lieuTravail->libelle);
                if(property_exists($offre->lieuTravail, 'commune') && $this->em->getRepository(Commune::class)->findOneBy(['codeInsee' => $offre->lieuTravail->commune])){
                    $nouvelle_offre->setCommune($this->em->getRepository(Commune::class)->findOneBy(['codeInsee' => $offre->lieuTravail->commune]));
                }else{
                    $villeQuery = $this->em->getRepository(Commune::class)->findBySlug(explode(' - ', $offre->lieuTravail->libelle)[1]);
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
                $nouvelle_offre->setValidation('valide');
                $nouvelle_offre->setVisibilite('visible');
                $this->em->persist($nouvelle_offre);
                $this->em->flush();
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