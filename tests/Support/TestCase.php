<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests\Support;

use BeastBytes\PDF\DocumentFactory;
use BeastBytes\PDF\DocumentFactoryInterface;
use BeastBytes\PDF\DocumentGenerator;
use BeastBytes\PDF\DocumentTemplate;
use BeastBytes\PDF\PdfInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionClass;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Test\Support\EventDispatcher\SimpleEventDispatcher;
use Yiisoft\View\View;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected const TEST_LOCALE = 'de_DE';
    protected const TEST_TEXT = 'Test text';

    private ?ContainerInterface $container = null;

    protected function get(string $id)
    {
        return $this
            ->getContainer()
            ->get($id);
    }

    protected static function getTestFilePath(): string
    {
        return sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . basename(str_replace('\\', '_', static::class))
        ;
    }

    private function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $tempDir = self::getTestFilePath();
            $eventDispatcher = new SimpleEventDispatcher();
            $view = new View($tempDir, $eventDispatcher);
            $documentTemplate = new DocumentTemplate($tempDir, '', '');
            $documentGenerator = new DocumentGenerator($view, $documentTemplate);
            $documentFactory = new DocumentFactory(DummyDocument::class, []);

            $this->container = new SimpleContainer([
                EventDispatcherInterface::class => $eventDispatcher,
                PdfInterface::class => new DummyPdf($documentFactory, $documentGenerator, $eventDispatcher),
                DocumentGenerator::class => new DocumentGenerator($view, $documentTemplate),
                DocumentTemplate::class => $documentTemplate,
                DocumentFactoryInterface::class => $documentFactory,
                View::class => $view,
            ]);
        }

        return $this->container;
    }

    /**
     * Gets an inaccessible object property.
     *
     * @param object $object
     * @param string $propertyName
     * @return mixed
     */
    protected function getInaccessibleProperty(object $object, string $propertyName): mixed
    {
        $class = new ReflectionClass($object);

        while (!$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }

        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $result = $property->getValue($object);
        $property->setAccessible(false);

        return $result;
    }

    protected function saveFile(string $filename, string $data): void
    {
        $path = dirname($filename);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($filename, $data);
    }
}
