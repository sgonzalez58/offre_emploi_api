<?php

namespace App\Form;

use App\Entity\Commune;
use App\Entity\OffreEmploi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class FormulaireOffreEmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intitule', TextType::class, [
                'required' => true,
                'label' => 'Titre de l\'offre *'
            ])
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
            ->add('appellationMetier', TextType::class, [
                'required' => true,
                'label' => 'Métier *',
                'attr' => ['placeholder' => 'Ex : Responsable de boutique, Agent / Agente d\'accueil, Auxiliaire de puériculture...']
            ])
            ->add('nomEntreprise', TextType::class)
            ->add('mailEntreprise', EmailType::class, [
                'required' => true,
            ])
            ->add('typeContrat', ChoiceType::class, [
                'label' => 'Type *',
                'choices' => [
                    'Contrat à durée déterminée' => 'CDD',
                    'Contrat à durée indéterminée' => 'CDI',
                    'CDD insertion' => 'DDI',
                    'CDI intérimaire ' => 'DIN',
                    'Franchise' => 'FRA',
                    'Profession libérale' => 'LIB',
                    'Mission intérimaire' => 'MIS',
                    'Contrat travail saisonnier' => 'SAI'
                ]
            ])
            ->add('duree',DateIntervalType::class, [
                'label' => 'Durée *',
                'with_years'=>false,
                'labels' => [
                    'months' => 'Mois',
                    'days' => 'Jours'
                ],
                'months' => range(1,19),
                'mapped' => false,
                'input' => 'array'
            ])
            ->add('natureContrat', ChoiceType::class, [
                'label' => 'Nature *',
                'choices' => [
                    'Contrat de professionnalisation' => 'Cont. professionnalisation',
                    'Contrat d\'apprentissage' => 'Contrat apprentissage',
                    'Contrat d\'engagement educatif' => 'Contrat d`\'Engagement Educatif',
                    'Contrat d\'usage ' => 'Contrat d\'usage',
                    'Contrat de travail' => 'Contrat travail',
                    'Contrat d\'accompagnement dans l\'emploi' => 'CUI - CAE',
                    'Emploi non salarié' => 'Emploi non salarié',
                    'Insertion par l\'activité économique' => 'Insertion par l\'activ.éco'
                ]
            ])
            ->add('experienceLibelle', TextType::class, [
                'required' => true,
                'label' => 'Expérience *',
                'attr' => ['placeholder' => 'Ex : 1 an exigé, Débutant accepté, Expérience souhaitée de 2 ans...']
            ])
            ->add('montantSalaire', IntegerType::class, [
                'mapped'=>false
            ])
            ->add('periodeSalaire', ChoiceType::class, [
                'choices'=>[
                    'an' => 'Annuel',
                    'mois' => 'Mensuel',
                    'heure' => 'Horaire'
                ],
                'mapped'=>false
            ])
            
            ->add('dureeTravail', TextType::class, [
                'label' => 'Temps de travail par semaine',
                'attr' => ['placeholder' => 'Ex : 35H horaires normaux, 39H Travail en 3X8, 37H Travail samedi et dimanche...']
            ])
            ->add('alternance')
            ->add('nbPostes', IntegerType::class, [
                'label' => 'Nombre de poste à pourvoir*'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description*'
            ])
            ->add('commune', EntityType::class, [
                'required' => false,
                'class' => Commune::class,
                'choice_label' => 'nom_commune',
                'placeholder' => '',
                'help' => 'Si vous ne trouvez pas votre commune, vous pouvez rentrer la commune manuellement ou préciser le département ci-dessous.'
            ])
            ->add('villeLibelle', TextType::class, [
                'label' => 'Localisation',
                'required' => false
            ])
            
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreEmploi::class,
        ]);
    }
}
