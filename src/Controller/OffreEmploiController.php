<?php

namespace App\Controller;

use App\Entity\OffreEmploi;
use App\Form\FormulaireOffreEmploiType;
use App\Repository\CommuneRepository;
use App\Repository\OffreEmploiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
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
            'offres' => $offreEmploiRepository->findBy(['validation' => 'valide'], [], $nb_offres),
            'villes' => $communeRepository->findBy([], ['nomCommune' => 'ASC']),
            'max_page' => ceil(count($offreEmploiRepository->findAll()) / $nb_offres)
        ]);
    }

    /**
     * @Route("/offreEmploi/getVille", name="getByVille_offre_emploi")
     */
    public function getByVille(OffreEmploiRepository $offreEmploiRepository, Request $request, CommuneRepository $communeRepository): Response
    {
        $session = $request->getSession();
        $nb_offres = $session->get('nb_offres', 50);
        $page = $request->request->get('page', 1);
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
        $jsonData['info'] = ['nbOffres' => $nb_offres_demandees, 'nbOffresPage' => $nb_offres, 'pageActuelle' => (int)$page, 'pageMax' => ceil($nb_offres_demandees / $nb_offres)];
        foreach($offres as $offre){
            if($offre->getVilleLibelle() && $offre->getVilleLibelle() != 'Non renseigné'){
                $nomVille = explode('- ', $offre->getVilleLibelle())[1];
            }
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
        $page = $request->request->get('page', 1);
        $offres = $offreEmploiRepository->findBy(['validation' => 'valide'], [], $nb_offres, ($page - 1) * $nb_offres);
        $nb_offres_max = count($offreEmploiRepository->findBy(['validation' => 'valide']));
        $jsonData = [];
        $idx = 0;
        $jsonData['info'] = ['nbOffres' => $nb_offres_max, 'nbOffresPage' => $nb_offres, 'pageActuelle' => (int)$page, 'pageMax' => ceil($nb_offres_max / $nb_offres)];
        foreach($offres as $offre){
            if($offre->getVilleLibelle() && $offre->getVilleLibelle() != 'Non renseigné'){
                $nomVille = explode('- ', $offre->getVilleLibelle())[1];
            }
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
     * @Route("/offreEmploi/creer", name="creer_offre_emploi")
     */
    public function creer(OffreEmploiRepository $offreEmploiRepository, Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login');
        }
        $offre = new OffreEmploi;
        $offre->setValidation('en attente');
        $offre->setDateDeCreation(new \Datetime());
        $offre->setDateActualisation(new \Datetime());
        $form = $this->createForm(FormulaireOffreEmploiType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $offre = $form->getData();
            $offre->setUser($user);
            $contratLibelle = '';
            switch($form['typeContrat']->getData()){
                case 'CDD':
                    $contratLibelle = 'Contrat à durée déterminée';
                    break;
                case 'CDI':
                    $contratLibelle = 'Contrat à durée indéterminée';
                    break;
                case 'DDI':
                    $contratLibelle = 'CDD insertion';
                    break;
                case 'DIN':
                    $contratLibelle = 'CDI intérimaire';
                    break;
                case 'FRA':
                    $contratLibelle = 'Franchise';
                    break;
                case 'LIB':
                    $contratLibelle = 'Profession libérale';
                    break;
                case 'MIS':
                    $contratLibelle = 'Mission intérimaire';
                    break;
                case 'SAI':
                    $contratLibelle = 'Contrat travail saisonnier';
                    break;
            }
            if($form['duree']->getData()['months'] == ''){
                $offre->setTypeContratLibelle( $contratLibelle . ' - ' . $form['duree']->getData()['days'] . ' Jour(s)');
            }else{
                $offre->setTypeContratLibelle( $contratLibelle . ' - ' . $form['duree']->getData()['months'] . ' Mois');
            }
            if($form['montantSalaire']->getData() != ''){
                $offre->setSalaire( $form['periodeSalaire']->getData() . ' de ' .  $form['montantSalaire']->getData() . 'Euros.');
            }
            if($form['latitude']->getData() == ''){
                if($offre->getCommune()){
                    $offre->setLatitude($offre->getCommune()->getLatitude());
                    $offre->setLongitude($offre->getCommune()->getLongitude());
                }
            }
            if( $form['villeLibelle']->getData() == ''){
                if($offre->getCommune()){
                    $offre->setVilleLibelle(substr($offre->getCommune()->getCodePostal(), 0, 2). ' - ' . strtoupper($offre->getCommune()->getNomCommune()));
                }else{
                    $offre->setVilleLibelle('Non renseigné');
                }
            }
            $offre->setVisibilite('visible');
            $offreEmploiRepository->add($offre, true);
            
            $this->addFlash('ajout', 'Votre demande d\'offre d\'emploi a bien été reçue. Elle apparaitra sur le site une fois validée.');
            return $this->redirectToRoute('app_offre_emploi');
        }
        return $this->renderForm('offreEmploi/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/offreEmploi/admin", name="gestion_offre_emploi")
     */
    public function gestion(OffreEmploiRepository $offreEmploiRepository): Response
    {
        return $this->render('offreEmploi/gestion.html.twig', [
            'offre' => $offreEmploiRepository->findAll()
        ]);
    }

    /**
     * @Route("/offreEmploi/admin/getOffresUsers", name="gestion_get_offre_emploi_users")
     */
    public function gestion_get_offres_users(OffreEmploiRepository $offreEmploiRepository): Response
    {
        $offres = $offreEmploiRepository->findBy(['id_pole_emploi'=>null]);
        $jsonData = [];
        $idx = 0;
        foreach($offres as $offre){
            if($offre->getVilleLibelle() && $offre->getVilleLibelle() != 'Non renseigné'){
                $nomVille = explode('- ', $offre->getVilleLibelle())[1];
            }else{
                if($offre->getLatitude()){
                    $nomVille ='Ville non précisée';
                }else{
                    $nomVille = 'Localisation inconnue!';
                }
            }
            if($offre->getNomEntreprise()){
                $nomEntreprise = $offre->getNomEntreprise();
            }else{
                $nomEntreprise = 'Non précisé';
            }
            $dateDemande = $offre->getDateActualisation();
            $jsonData[$idx++] = ['id' => $offre->getId(), 'intitule' => $offre->getIntitule(), 'nomVille' => $nomVille, 'nomEntreprise' => $nomEntreprise, 'dateDemande' => $dateDemande, 'etat' => $offre->getValidation()];
        }
        return new JsonResponse($jsonData);
    }

    /**
     * @Route("/offreEmploi/admin/refuserOffre/{id}", name="gestion_refuser_offre_emploi")
     */
    public function refuserOffre(int $id, OffreEmploiRepository $offreEmploiRepository, ManagerRegistry $doctrine, Request $request, MailerInterface $mailer): Response
    {
        $offre = $offreEmploiRepository->find($id);
        if (!$offre){
            return new JsonResponse('L\'offre n\'existe pas. Erreure lors de l\'envoie de l\'id.', 500);
        }
        $raison = '';
        if ($request->isXmlHttpRequest()){
            $raison = $request->request->get('raison');
            if(!$raison){
                return new JsonResponse('Erreure ajax. La raison du refus n\'a pas été donnée.', 500);
            }
        }else{
            if($request->request->get('localisation')){
                $raison .= 'La localisation et le nom de la commune ne sont pas renseignés.<br>';
            }
            if($request->request->get('entreprise')){
                $raison .= 'Le nom de l\'entreprise n\'est pas renseigné.<br>';
            }
            $raison .= $request->request->get('raison_personnalisee').'<br>';
        }
        $offre->setValidation('refus');
        $offre->setVisibilite('non visible');
        $doctrine->getManager()->flush();
        $email = (new TemplatedEmail())
            ->from('no-reply@iti-conseil.com')
            ->to($offre->getMailEntreprise())
            ->subject('Refus offre d\'emploi.')
            ->htmlTemplate('offreEmploi/mailRefus.html.twig')
            ->context([
                'raison' => $raison
            ])
        ;
        $mailer->send($email);
        if ($request->isXmlHttpRequest()){
            return new JsonResponse('Offre refusée et mail envoyé.');
        }else{
            $this->addFlash('reponse', 'L\'offre d\'emploi n°'.$id.' a bien été refusée.');
            return $this->redirectToRoute('gestion_offre_emploi');
        }
    }

    /**
     * @Route("/offreEmploi/admin/accepterOffre/{id}", name="gestion_accepter_offre_emploi")
     */
    public function accepterOffre(int $id, OffreEmploiRepository $offreEmploiRepository, ManagerRegistry $doctrine, Request $request, MailerInterface $mailer): Response
    {
        $offre = $offreEmploiRepository->find($id);
        if (!$offre){
            return new JsonResponse('L\'offre n\'existe pas. Erreure lors de l\'envoie de l\'id.', 500);
        }
        $raison = $request->request->get('raison_personnalisee').'<br>';
        $offre->setValidation('valide');
        $offre->setVisibilite('visible');
        $doctrine->getManager()->flush();
        $email = (new TemplatedEmail())
            ->from('no-reply@iti-conseil.com')
            ->to($offre->getMailEntreprise())
            ->subject('Validation offre d\'emploi.')
            ->htmlTemplate('offreEmploi/mailValide.html.twig')
            ->context([
                'raison' => $raison
            ])
        ;
        $mailer->send($email);
        $this->addFlash('reponse', 'L\'offre d\'emploi n°'.$id.' a bien été validée.');
        return $this->redirectToRoute('gestion_offre_emploi');
    }

    /**
     * @Route("/offreEmploi/admin/{id}", name="admin_fiche_offre_emploi")
     */
    public function adminFiche(int $id, OffreEmploiRepository $offreEmploiRepository): Response
    {
        return $this->render('offreEmploi/fiche.html.twig', [
            'offre' => $offreEmploiRepository->find($id),
            'admin' => true,
            'user' => false
        ]);
    }

    /**
     * @Route("/offreEmploi/{id}", name="fiche_offre_emploi")
     */
    public function fiche(int $id, OffreEmploiRepository $offreEmploiRepository): Response
    {
        return $this->render('offreEmploi/fiche.html.twig', [
            'offre' => $offreEmploiRepository->find($id),
            'admin' => false,
            'user' => false,
        ]);
    }

    /**
     * @Route("/user/{id}/offreEmploi", name="client_offre_emploi")
     */
    public function clientOffre(int $id, OffreEmploiRepository $offreEmploiRepository): Response
    {
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();
        if(!$user || $user->getId() != $id){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('offreEmploi/userGestion.html.twig', [
            'offre' => $offreEmploiRepository->findBy(['user'=> $id])
        ]);
    }

    /**
     * @Route("/user/offreEmploi/toggleVisibilite/{id}", name="user_visibilite_offre_emploi")
     */
    public function toggleVisibilite(int $id, OffreEmploiRepository $offreEmploiRepository, ManagerRegistry $doctrine): Response
    {
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();
        $offre = $offreEmploiRepository->find($id);
        if(!$offre){
            return new JsonResponse('Cette offre n\'existe pas. L\'id est incorrecte ou l\'offre a été supprimée.', 500);
        }
        if(!$user || $user->getId() != $offre->getUser()->getId()){
            return new JsonResponse('Vous n\'avez pas les droits pour modifier cette offre.', 500);
        }
        if($offre->getVisibilite() == 'visible')
            $offre->setVisibilite('non visible');
        else
            $offre->setVisibilite('visible');
        $doctrine->getManager()->flush();
        return new JsonResponse('La visibilite a été changée.');
    }

    /**
     * @Route("/user/{id}/offreEmploi/getOffres/", name="user_get_offre_emploi")
     */
    public function userGetOffres(int $id, OffreEmploiRepository $offreEmploiRepository): Response
    {
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();
        if(!$user || $user->getId() != $id){
            return new JsonResponse('Vous n\'avez pas les droits.', 500);
        }
        $offres = $offreEmploiRepository->findBy(['user'=>$id]);
        $jsonData = [];
        $idx = 0;
        foreach($offres as $offre){
            if($offre->getVilleLibelle() && $offre->getVilleLibelle() != 'Non renseigné'){
                $nomVille = explode('- ', $offre->getVilleLibelle())[1];
            }else{
                if($offre->getLatitude()){
                    $nomVille ='Ville non précisée';
                }else{
                    $nomVille = 'Localisation inconnue!';
                }
            }
            if($offre->getNomEntreprise()){
                $nomEntreprise = $offre->getNomEntreprise();
            }else{
                $nomEntreprise = 'Non précisé';
            }
            $dateCreation = $offre->getDateActualisation();
            $jsonData[$idx++] = ['id' => $offre->getId(), 'intitule' => $offre->getIntitule(), 'nomVille' => $nomVille, 'nomEntreprise' => $nomEntreprise, 'dateCreation' => $dateCreation, 'etat' => $offre->getValidation(), 'visibilite' => $offre->getVisibilite()];
        }
        return new JsonResponse($jsonData);
    }

    /**
     * @Route("/user/{idclient}/offreEmploi/{id}", name="user_fiche_offre_emploi")
     */
    public function userFicheOffre(int $idclient, int $id, OffreEmploiRepository $offreEmploiRepository): Response
    {
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();
        if(!$user || $user->getId() != $idclient){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('offreEmploi/fiche.html.twig', [
            'offre' => $offreEmploiRepository->find($id),
            'admin' => false,
            'user' => true
        ]);
    }

    /**
     * @Route("/user/offreEmploi/creer", name="user_creer_offre_emploi")
     */
    public function userCreerOffre(OffreEmploiRepository $offreEmploiRepository, Request $request): Response
    {
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login');
        }
        $offre = new OffreEmploi;
        $offre->setValidation('en attente');
        $offre->setDateDeCreation(new \Datetime());
        $offre->setDateActualisation(new \Datetime());
        $form = $this->createForm(FormulaireOffreEmploiType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $offre = $form->getData();
            $offre->setUser($user);
            $contratLibelle = '';
            switch($form['typeContrat']->getData()){
                case 'CDD':
                    $contratLibelle = 'Contrat à durée déterminée';
                    break;
                case 'CDI':
                    $contratLibelle = 'Contrat à durée indéterminée';
                    break;
                case 'DDI':
                    $contratLibelle = 'CDD insertion';
                    break;
                case 'DIN':
                    $contratLibelle = 'CDI intérimaire';
                    break;
                case 'FRA':
                    $contratLibelle = 'Franchise';
                    break;
                case 'LIB':
                    $contratLibelle = 'Profession libérale';
                    break;
                case 'MIS':
                    $contratLibelle = 'Mission intérimaire';
                    break;
                case 'SAI':
                    $contratLibelle = 'Contrat travail saisonnier';
                    break;
            }
            if($form['duree']->getData()['months'] == ''){
                $offre->setTypeContratLibelle( $contratLibelle . ' - ' . $form['duree']->getData()['days'] . ' Jour(s)');
            }else{
                $offre->setTypeContratLibelle( $contratLibelle . ' - ' . $form['duree']->getData()['months'] . ' Mois');
            }
            if($form['montantSalaire']->getData() != ''){
                $offre->setSalaire( $form['periodeSalaire']->getData() . ' de ' .  $form['montantSalaire']->getData() . 'Euros.');
            }
            if($form['latitude']->getData() == ''){
                if($offre->getCommune()){
                    $offre->setLatitude($offre->getCommune()->getLatitude());
                    $offre->setLongitude($offre->getCommune()->getLongitude());
                }
            }
            if( $form['villeLibelle']->getData() == ''){
                if($offre->getCommune()){
                    $offre->setVilleLibelle(substr($offre->getCommune()->getCodePostal(), 0, 2). ' - ' . strtoupper($offre->getCommune()->getNomCommune()));
                }else{
                    $offre->setVilleLibelle('Non renseigné');
                }
            }
            $offre->setVisibilite('non visible');
            $offreEmploiRepository->add($offre, true);
            
            return $this->redirectToRoute('client_offre_emploi', ['id' => $user->getId()]);
        }
        return $this->renderForm('offreEmploi/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/user/{idclient}/offreEmploi/modifier/{id}", name="user_modifer_offre_emploi")
     */
    public function userModifierOffre(int $idclient, int $id, EntityManagerInterface $em, OffreEmploiRepository $offreEmploiRepository, Request $request): Response
    {
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();
        if(!$user || $user->getId() != $idclient){
            return $this->redirectToRoute('app_login');
        }
        $offre = $offreEmploiRepository->find($id);
        if(!$offre){
            return new Response('Offre d\'emploi non trouvé', 500);
        }
        $offre->setValidation('en attente');
        $offre->setDateActualisation(new \Datetime());
        $form = $this->createForm(FormulaireOffreEmploiType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $offre = $form->getData();
            $offre->setUser($user);
            $contratLibelle = '';
            switch($form['typeContrat']->getData()){
                case 'CDD':
                    $contratLibelle = 'Contrat à durée déterminée';
                    break;
                case 'CDI':
                    $contratLibelle = 'Contrat à durée indéterminée';
                    break;
                case 'DDI':
                    $contratLibelle = 'CDD insertion';
                    break;
                case 'DIN':
                    $contratLibelle = 'CDI intérimaire';
                    break;
                case 'FRA':
                    $contratLibelle = 'Franchise';
                    break;
                case 'LIB':
                    $contratLibelle = 'Profession libérale';
                    break;
                case 'MIS':
                    $contratLibelle = 'Mission intérimaire';
                    break;
                case 'SAI':
                    $contratLibelle = 'Contrat travail saisonnier';
                    break;
            }
            if($form['duree']->getData()['months'] == ''){
                $offre->setTypeContratLibelle( $contratLibelle . ' - ' . $form['duree']->getData()['days'] . ' Jour(s)');
            }else{
                $offre->setTypeContratLibelle( $contratLibelle . ' - ' . $form['duree']->getData()['months'] . ' Mois');
            }
            if($form['montantSalaire']->getData() != ''){
                $offre->setSalaire( $form['periodeSalaire']->getData() . ' de ' .  $form['montantSalaire']->getData() . 'Euros.');
            }
            if($form['latitude']->getData() == ''){
                if($offre->getCommune()){
                    $offre->setLatitude($offre->getCommune()->getLatitude());
                    $offre->setLongitude($offre->getCommune()->getLongitude());
                }
            }
            if( $form['villeLibelle']->getData() == ''){
                if($offre->getCommune()){
                    $offre->setVilleLibelle(substr($offre->getCommune()->getCodePostal(), 0, 2). ' - ' . strtoupper($offre->getCommune()->getNomCommune()));
                }else{
                    $offre->setVilleLibelle('Non renseigné');
                }
            }
            $offre->setVisibilite('non visible');
            $em->flush();
            
            return $this->redirectToRoute('user_fiche_offre_emploi', ['idclient' => $idclient, 'id' => $id]);
        }
        return $this->renderForm('offreEmploi/new.html.twig', [
            'form' => $form,
        ]);
    }
    /**
     * @Route("/user/{idclient}/offreEmploi/supprimer/{id}", name="user_supprimer_offre_emploi")
     */
    public function userSupprimerOffre(int $idclient, int $id, OffreEmploiRepository $offreEmploiRepository):Response
    {
        /** @var \App\Entity\Users $user */
        $user = $this->getUser();
        if(!$user || $user->getId() != $idclient){
            return new JsonResponse('Vous n\'avez pas les droits.', 500);
        }
        $offre = $offreEmploiRepository->find($id);
        if(!$offre){
            return new JsonResponse('Offre d\'emploi non trouvé', 500);
        }
        $offreEmploiRepository->remove($offre, true);
        return new JsonResponse('Offre supprimée.');
    }
}
