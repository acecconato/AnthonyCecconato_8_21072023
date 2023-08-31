<?php

declare(strict_types=1);

namespace App\Twig\Component\Form;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'user-form', template: 'component/form/user_form.html.twig')]
class UserFormComponent
{
    public FormView $form;
}
