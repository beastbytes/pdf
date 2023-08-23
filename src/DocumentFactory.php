<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF;

use InvalidArgumentException;

class DocumentFactory implements DocumentFactoryInterface
{
    public const INVALID_CLASS_EXCEPTION_MESSAGE = 'Class "%s" does not implement "%s".';

    /**
     * @var string The document class name.
     *
     * @psalm-var class-string<DocumentInterface>
     */
    private string $class;

    /**
     * @param string $class The document class name.
     * @param array $args Constructor arguments for the PDF document as ['name' => $value]
     * @throws InvalidArgumentException If the class does not implement `DocumentInterface`.
     */
    public function __construct(string $class, private array $args)
    {
        if (!is_subclass_of($class, DocumentInterface::class)) {
            throw new InvalidArgumentException(sprintf(
                self::INVALID_CLASS_EXCEPTION_MESSAGE,
                $class,
                DocumentInterface::class,
            ));
        }

        $this->class = $class;
    }

    public function create(): DocumentInterface
    {
        return new $this->class(...$this->args);
    }
}
