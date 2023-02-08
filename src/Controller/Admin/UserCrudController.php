<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};
use Symfony\Component\Form\{FormBuilderInterface, FormEvent, FormEvents};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\{TextField, TextEditorField, AssociationField, IdField};
use App\Services\RolesHelper;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        
        yield TextField::new('username', "Nom d'utilisateur");
        
        
        // créer un champ password avec des *****. mapped permet de ne pas récupérer le mot de passe
        
        yield TextField::new('password')
        ->setFormType(RepeatedType::class)
        ->setFormTypeOptions([
        'type' => PasswordType::class,
        'first_options' => ['label' => 'Mot de passe'],
        'second_options' => ['label' => 'Répétez le mot de passe'],
        'mapped' => false, // permet de ne pas récupérer le mot de passe
        ])
        ->setRequired($pageName === Crud::PAGE_NEW)->onlyOnForms();

        // permet de choisir plusieurs rôles
        
        yield ChoiceField::new('roles')->setChoices($this->rolesHelper->getRoles())->allowMultipleChoices(); 

    }

    public function __construct(private RolesHelper $rolesHelper, private UserPasswordHasherInterface $userPasswordHasher){

    }


    private function manageRoles($form):array{
        return array_values($form->get('roles')->getData());
    }


    private function manageField() {
        return function($event) {
                $form = $event->getForm();
                if (!$form->isValid()) {
                return;
                }
                $password = $form->get('password')->getData();
                if ($password === null) {
                return;
                }
                $form->getData()->setPassword($this->hashPassword($password));
                $form->getData()->setRoles($this->manageRoles($form));
            };
        }


        private function addFieldEvantListener(FormBuilderInterface $formBuilder): FormBuilderInterface
        {

            return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->manageField());

        }

        public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
        {

        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
            return $this->addFieldEvantListener($formBuilder);

        }


        public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
        {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);

            return $this->addFieldEvantListener($formBuilder);

        }

        private function hashPassword($password): string {
            return $this->userPasswordHasher->hashPassword($this->getUser(), $password);
        }
        


}
