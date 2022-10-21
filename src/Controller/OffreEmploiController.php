<?php

namespace App\Controller;

use App\Repository\CommuneRepository;
use App\Repository\OffreEmploiRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OffreEmploiController extends AbstractController
{
    /**
     * @Route("/offreEmploi", name="app_offre_emploi")
     */
    public function index(OffreEmploiRepository $offreEmploiRepository, CommuneRepository $communeRepository, Request $request): Response
    {
        $session = $request->getSession();
        $nb_offres = $session->get('nb_offres', 50);
        return $this->render('offreEmploi/index.html.twig', [
            'offres' => $offreEmploiRepository->findBy([], [], $nb_offres),
            'villes' => $communeRepository->findBy([], ['nomCommune' => 'ASC']),
            'max_page' => count($offreEmploiRepository->findAll()) / $nb_offres
        ]);
    }

    /**
     * @Route("/offreEmploi/getVille", name="getByVille_offre_emploi")
     */
    public function getByVille(OffreEmploiRepository $offreEmploiRepository, Request $request, CommuneRepository $communeRepository): Response
    {
        $session = $request->getSession();
        $nb_offres = $session->get('nb_offres', 50);
        $page = $request->query->get('page', 1);
        $ville_id = $request->request->get('ville');
        $distance = $request->request->get('distance');
        if(!$ville_id){
            return new JsonResponse("L'id de la ville n'a pas été envoyé. Erreure lors de la demande ajax.", 500);
        }
        if(!$distance){
            return new JsonResponse("La distance maximale de la recherche n'a pas été envoyée. Erreure lors de la demande ajax.", 500);
        }
        if($distance == 'aucune'){
            $offres = $offreEmploiRepository->findByCommunes(array($communeRepository->find($ville_id)), $nb_offres, ($page - 1) * $nb_offres);
            $nb_offres_demandees = count($offreEmploiRepository->findByCommunes(array($communeRepository->find($ville_id))));
        }else{
            $offres = array();
            $ville_cible = array();
            $ville_a_trier = $communeRepository->findAll();

            foreach($ville_a_trier as $commune){
                if($commune->getId() == $ville_id){
                    array_push($ville_cible, $commune); 
                }else{
                    $villeFrom = $communeRepository->find($ville_id);
                    $villeTo = $commune;
                    $latFrom = deg2rad($villeFrom->getLatitude());
                    $lonFrom = deg2rad($villeFrom->getLongitude());
                    $latTo = deg2rad($villeTo->getLatitude());
                    $lonTo = deg2rad($villeTo->getLongitude());
                    $lonDelta = $lonTo - $lonFrom;
                    $a = pow(cos($latTo) * sin($lonDelta), 2) +
                        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
                    $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
                    $angle = atan2(sqrt($a), $b);
                    $distanceVilles = $angle * 6371;
                    if($distanceVilles < $distance){
                        array_push($ville_cible, $commune);
                    }
                }
            }
            $offres = $offreEmploiRepository->findByCommunes($ville_cible, $nb_offres, ($page - 1) * $nb_offres);
            $nb_offres_demandees = count($offreEmploiRepository->findByCommunes($ville_cible));
        }
        $jsonData = [];
        $idx = 0;
        $jsonData['info'] = ['nbOffres' => $nb_offres_demandees, 'nbOffresPage' => $nb_offres, 'pageActuelle' => $page, 'pageMax' => ceil($nb_offres_demandees / $nb_offres)];
        foreach($offres as $offre){
            $nomVille = explode('- ', $offre->getVilleLibelle())[1];
            if($offre->getLatitude()){
                $lienMap = 'https://www.openstreetmap.org/?mlat=' . $offre->getLatitude() . '&mlon=' . $offre->getLongitude() . '#map=17/' . $offre->getLatitude() . '/' . $offre->getLongitude() . '&layers=N';
            }else{
                $lienMap = 'aucun';
            }
            if(strlen($offre->getDescription()) > 150){
                $description = substr(htmlentities($offre->getDescription()), 0, 149) . '...';
            }else{
                $description = $offre->getDescription();
            }
            if($offre->getNomEntreprise()){
                $nomEntreprise = $offre->getNomEntreprise();
            }else{
                $nomEntreprise = 'Aucun';
            }
            $jsonData[$idx++] = ['id' => $offre->getId(), 'intitule' => $offre->getIntitule(), 'nomVille' => $nomVille, 'lienMap' => $lienMap, 'description' => $description, 'nomEntreprise' => $nomEntreprise, 'lienOrigineOffre' => $offre->getOrigineOffre()];
        }
        return new JsonResponse($jsonData);
    }

    /**
     * @Route("/offreEmploi/getAll", name="getAll_offre_emploi")
     */
    public function getAll(OffreEmploiRepository $offreEmploiRepository, Request $request): Response
    {
        $session = $request->getSession();
        $nb_offres = $session->get('nb_offres', 50);
        $page = $request->query->get('page', 1);
        $offres = $offreEmploiRepository->findBy([], [], $nb_offres, ($page - 1) * $nb_offres);
        $jsonData = [];
        $idx = 0;
        foreach($offres as $offre){
            $nomVille = explode('- ', $offre->getVilleLibelle())[1];
            if($offre->getLatitude()){
                $lienMap = 'https://www.openstreetmap.org/?mlat=' . $offre->getLatitude() . '&mlon=' . $offre->getLongitude() . '#map=17/' . $offre->getLatitude() . '/' . $offre->getLongitude() . '&layers=N';
            }else{
                $lienMap = 'aucun';
            }
            if(strlen($offre->getDescription()) > 150){
                $description = substr(htmlentities($offre->getDescription()), 0, 148) . '...';
            }else{
                $description = $offre->getDescription();
            }
            if($offre->getNomEntreprise()){
                $nomEntreprise = $offre->getNomEntreprise();
            }else{
                $nomEntreprise = 'Aucun';
            }
            $jsonData[$idx++] = ['id' => $offre->getId(), 'intitule' => $offre->getIntitule(), 'nomVille' => $nomVille, 'lienMap' => $lienMap, 'description' => $description, 'nomEntreprise' => $nomEntreprise, 'lienOrigineOffre' => $offre->getOrigineOffre()];
        }
        return new JsonResponse($jsonData);
    }

    /**
     * @Route("/offreEmploi/{id}", name="fiche_offre_emploi")
     */
    public function fiche(int $id, OffreEmploiRepository $offreEmploiRepository): Response
    {
        return $this->render('offreEmploi/fiche.html.twig', [
            'offre' => $offreEmploiRepository->find($id)
        ]);
    }
}
