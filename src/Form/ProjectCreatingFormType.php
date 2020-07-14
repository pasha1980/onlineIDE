<?php


namespace App\Form;


use App\Entity\ProjectInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectCreatingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projectName', TextType::class, [
                'required' => true,
                'label' => 'Name of the new project',

            ])
            ->add('projectType', ChoiceType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'What type of this project ?',
                'choices' => [
                    'Console application' => 1,
                    'Website' => 2,
                    'Front-end' => 3,
                ],
            ])
            ->add('isOpen', ChoiceType::class, [
                'required' => true,
                'label' => 'Close/Open',
                'choices' => [
                    'Open' => true,
                    'Close' => false,
                ]
            ])
            ->add('Submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectInfo::class,
        ]);
    }
}