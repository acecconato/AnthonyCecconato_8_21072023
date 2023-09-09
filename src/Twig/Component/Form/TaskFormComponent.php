<?php

declare(strict_types=1);

namespace App\Twig\Component\Form;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'task-form', template: 'component/form/task_form.html.twig')]
class TaskFormComponent
{
    public FormView $form;
}
