<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'header-image', template: 'component/design_system/header_image.html.twig')]
final class HeaderImageComponent
{
    public string $width = '100%';
    public string $height = 'auto';
    public bool $crop = false;
}
