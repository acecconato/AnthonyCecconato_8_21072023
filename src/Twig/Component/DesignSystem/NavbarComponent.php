<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'navbar', template: '/component/design_system/navbar.html.twig')]
final class NavbarComponent
{
}
