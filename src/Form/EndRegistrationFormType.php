<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EndRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $builder->getData();
        $builder
            ->add('nickname', TextType::class, [
                'disabled' => true,
                'data' => $user->getNickname(),
            ])
            ->add('email', EmailType::class, [
                'data' => $user->getEmail(),
            ])
            ->add('company', TextType::class, [
                'label' => 'Company',
                'required' => false,
                'data' => $user->getCompany(),
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Describe yourself',
                'data' => $user->getDescription(),
                'required' => false,
            ])
            ->add('Submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}