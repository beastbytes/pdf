<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF;

use BadMethodCallException;
use Stringable;

abstract class Document implements DocumentInterface, Stringable
{
    public const BAD_METHOD_EXCEPTION = '`{name}` is not a valid method';

    protected array $helpers = [];
    /**
     * @var object $pdf The pdf library
     */
    protected object $pdf;

    /**
     * Called if an unrecognised method is called
     *
     * Look for the method in the pdf class and then the helpers; if the method is not found pass to the parent
     * to resolve.
     *
     * Do not call this method directly as it is a PHP magic method that will be implicitly called when an unknown
     * method is being invoked.
     *
     * @param string $name Method name
     * @param array $params Method parameters
     * @return mixed Result of the method call
     * @throws \BadMethodCallException If the method does not exist in the PDF library or helpers
     */
    public function __call(string $name, array $params): mixed
    {
        if (method_exists($this->pdf, $name)) {
            return call_user_func_array([$this->pdf, $name], $params);
        }

        foreach ($this->helpers as $helper) {
            if (method_exists($helper, $name)) {
                return call_user_func_array([$helper, $name], $params);
            }
        }

        throw new BadMethodCallException(strtr(self::BAD_METHOD_EXCEPTION, ['{name}' => $name]));
    }

    abstract public function __toString(): string;
    abstract public function getAuthor(): string;
    abstract public function getCreator(): string;
     abstract public function getCustomProperties(): array;
    abstract public function getKeywords(): array;
    abstract public function getName(): string;
    abstract public function getSubject(): string;
    abstract public function getTitle(): string;
    abstract public function withAuthor(string $author): DocumentInterface;
    abstract public function withCreator(string $creator): DocumentInterface;
    abstract public function withCustomProperties(array $customProperties): DocumentInterface;
    abstract public function withKeywords(string ...$keywords): DocumentInterface;
    abstract public function withName(string $name): DocumentInterface;
    abstract public function withSubject(string $subject): DocumentInterface;
    abstract public function withTitle(string $title): DocumentInterface;

    public function withHelpers(...$helpers): DocumentInterface
    {
        $new = clone $this;

        foreach ($helpers as $helper) {
            $this->helpers[] = $helper;
        }

        return $new;
    }
}
