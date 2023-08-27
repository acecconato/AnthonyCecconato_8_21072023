<?php

declare(strict_types=1);

namespace App\Twig\Component;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'alert', template: 'component/design_system/alert.html.twig')]
class Alert
{
    public string $type = 'danger';
    public string $message;

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
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
