<?php

namespace App\Controller\Admin;

use App\Entity\Reclamo;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'Ingrese su email'),
            TextField::new('password', 'Contraseña')->onlyWhenCreating(),
            ChoiceField::new('roles', 'Roles de usuario')
            ->setChoices([

                'Admin' => 'ROLE_RECLAMO_ADMIN',
                'Usuario Energía' => 'ROLE_RECLAMO_GUARDIA_ENERGIA',
                'Usuario Saneamiento' => 'ROLE_RECLAMO_GUARDIA_SANEAMIENTO',
                'Usuario Atencion' => 'ROLE_RECLAMO_ATENCION_TELEFONISTA',
                'Usuario Reclamo' => 'ROLE_RECLAMO_CREACION',

            ])
            ->allowMultipleChoices(),
        ];
    }


}
