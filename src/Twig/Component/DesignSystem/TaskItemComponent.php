<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use App\Entity\Task;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(name: 'task-item', template: '/component/design_system/task_item.html.twig')]
final class TaskItemComponent
{
    public Task $task;

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['task']);
        $resolver->setAllowedTypes('task', Task::class);

        return $resolver->resolve($data);
    }
}
