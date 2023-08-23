<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests;

use BeastBytes\PDF\DocumentTemplate;
use BeastBytes\PDF\Tests\Support\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class DocumentTemplateTest extends TestCase
{
    #[DataProvider('constructorProvider')]
    public function test_constructor(string $viewPath): void
    {
        $template = new DocumentTemplate($viewPath);

        $this->assertSame($viewPath, $template->getViewPath());
    }

    public static function constructorProvider(): Generator
    {
        $tempDir = self::getTestFilePath() . DIRECTORY_SEPARATOR;

        foreach ([
            'foo' => ["{$tempDir}foo"],
            'bar' => ["{$tempDir}bar"],
            'baz' => ["{$tempDir}baz"],
        ] as $name => $args) {
            yield $name => $args;
        }
    }
}
