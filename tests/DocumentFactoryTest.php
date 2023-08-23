<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests;

use BeastBytes\PDF\DocumentFactory;
use BeastBytes\PDF\DocumentInterface;
use BeastBytes\PDF\Tests\Support\DummyDocument;
use BeastBytes\PDF\Tests\Support\TestCase;
use InvalidArgumentException;

final class DocumentFactoryTest extends TestCase
{
    public function test_create(): void
    {
        $factory = new DocumentFactory(DummyDocument::class, []);
        $this->assertInstanceOf(DummyDocument::class, $factory->create());
    }

    public function test_constructor_throw_exception_if_invalid_document_class(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            DocumentFactory::INVALID_CLASS_EXCEPTION_MESSAGE,
            self::class,
           DocumentInterface::class
        ));
        new DocumentFactory(self::class, []);
    }
}
