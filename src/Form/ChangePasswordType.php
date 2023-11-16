<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator){

    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastPassword', PasswordType::class, [
                'required' => true,
                'mapped' => false,
                'attr' => ['autocomplete' => 'last-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message_password'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('message_password_size'),
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmNewPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => $this->translator->trans('message_repeat_password'),
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => $this->translator->trans('new_password')],
                'second_options' => ['label' => $this->translator->trans('repeat_new_password')],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('message_password'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('message_password_size'),
                        'max' => 4096,
                    ]),
                ],
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'task_item',
        ]);
    }
}
