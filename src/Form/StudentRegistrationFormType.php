<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class StudentRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'email est obligatoire."]),
                    new Assert\Email(['message' => 'Email invalide.']),
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
            ])
            ->add('studentId', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le numéro étudiant est obligatoire.']),
                ],
            ])
            ->add('specialty', ChoiceType::class, [
                'choices' => [
                    'Choisir...'                       => '',
                    "Technologies d'Information (TI)"  => 'TI',
                    'Systèmes Informatiques (SI)'      => 'SI',
                    'Génie Logiciel (GL)'              => 'GL',
                    'Réseaux & Télécommunications'     => 'RT',
                    'Intelligence Artificielle'        => 'IA',
                    'Sécurité Informatique'            => 'SEC',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez choisir une spécialité.']),
                ],
            ])
            ->add('studyYear', ChoiceType::class, [
                'choices' => [
                    'Choisir...' => '',
                    'L1' => 'L1', 'L2' => 'L2', 'L3' => 'L3',
                    'M1' => 'M1', 'M2' => 'M2',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => "Veuillez choisir une année."]),
                ],
            ])
            ->add('skills', HiddenType::class, [
                'required' => false,
                'mapped'   => false,
            ])
            ->add('githubLink', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Url(['message' => 'Lien invalide.']),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'first_options'   => ['label' => 'Mot de passe'],
                'second_options'  => ['label' => 'Confirmer le mot de passe'],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints'     => [
                    new Assert\NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Assert\Length([
                        'min'        => 8,
                        'minMessage' => 'Minimum {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped'      => false,
                'constraints' => [
                    new Assert\IsTrue(['message' => 'Vous devez accepter les conditions.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}