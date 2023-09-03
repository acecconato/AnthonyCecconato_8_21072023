<?php

declare(strict_types=1);

namespace App\Tests\Integration\Twig\Component;

use App\Twig\Component\PaginationComponent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

class PaginationComponentTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    public function testComponentMount(): void
    {
        $component = $this->mountTwigComponent('pagination', [
            'total_items' => 10,
            'total_pages' => 3,
            'page' => 1,
            'route' => 'app_users',
        ]);

        self::assertInstanceOf(PaginationComponent::class, $component);
        self::assertSame(10, $component->total_items);
        self::assertSame(3, $component->total_pages);
        self::assertSame(1, $component->page);
        self::assertSame('app_users', $component->route);
    }

    public function testComponentRenders(): void
    {
        $rendered = $this->renderTwigComponent('pagination', [
            'total_items' => 10,
            'total_pages' => 3,
            'page' => 1,
            'route' => 'app_users',
        ]);

        self::assertCount(4, $rendered->crawler()->filter('ul li'));
        self::assertStringContainsString('Suivant', $rendered->toString());
    }

    /**
     * @dataProvider MissingOptionsExceptionProvider
     *
     * @param array<string, mixed> $datas
     */
    public function testComponentOptionsResolver(array $datas): void
    {
        self::expectException(MissingOptionsException::class);
        $this->mountTwigComponent('pagination', $datas);
    }

    public static function MissingOptionsExceptionProvider(): \Generator
    {
        yield [
            [
//                'total_items' => 10,
                'total_pages' => 3,
                'page' => 1,
                'route' => 'app_users',
            ],
        ];

        yield [
            [
                'total_items' => 10,
//                'total_pages' => 3,
                'page' => 1,
                'route' => 'app_users',
            ],
        ];

        yield [
            [
                'total_items' => 10,
                'total_pages' => 3,
//                'page' => 1,
                'route' => 'app_users',
            ],
        ];

        yield [
            [
                'total_items' => 10,
                'total_pages' => 3,
                'page' => 1,
//                'route' => 'app_users',
            ],
        ];
    }
}
