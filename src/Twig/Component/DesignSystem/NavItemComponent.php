<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(name: 'nav-item', template: '/component/design_system/nav_item.html.twig')]
final class NavItemComponent
{
    public string $label;
    public string $route;

    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['label', 'route']);

        return $resolver->resolve($data);
    }

    public function isActive(): bool
    {
        return $this->requestStack->getMainRequest()?->attributes->get('_route') === $this->route;
    }
}
