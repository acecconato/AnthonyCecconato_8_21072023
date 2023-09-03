<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(name: 'alert', template: 'component/design_system/alert.html.twig')]
final class AlertComponent
{
    public string $type = 'danger';
    public string $message;

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(['type' => 'danger']);

        $resolver->setAllowedValues('type', ['success', 'info', 'alert', 'danger']);

        $resolver->setRequired('message');
        $resolver->setAllowedTypes('message', 'string');

        return $resolver->resolve($data);
    }
}
