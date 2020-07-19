<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditFileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', TextareaType::class, [
                'data' => $options['data']['file'],
                'label' => $options['data']['fileName'],
                'disabled' => !$options['data']['legalToChange'],
                'attr' => [
                    'rows' => '18',
                ],
            ]);
        if($options['data']['legalToChange']) {
            $builder->add('Submit', SubmitType::class, [
                'label' => 'Save',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}