<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF;

use BadMethodCallException;
use BeastBytes\PDF\Helper\Helper;
use Stringable;

abstract class Document implements DocumentInterface, Stringable
{
    public const BAD_METHOD_EXCEPTION = 'Method `%s()` not found';

    protected array $helpers = [];
    /**
     * @var object $pdf The pdf library
     */
    protected object $pdf;

    public function __construct()
    {

    }

    /**
     * Called if an unrecognised method is called
     *
     * Look for the method in the helpers then the pdf class.
     *
     * Do not call this method directly as it is a PHP magic method that will be implicitly called when an unknown
     * method is being invoked.
     *
     * @param string $name Method name
     * @param array $params Method parameters
     * @return mixed Result of the method call
     * @throws BadMethodCallException If the method does not exist in the PDF library or helpers
     */
    public function __call(string $name, array $params): mixed
    {
        foreach ($this->helpers as $helper) {
            if (method_exists($helper, $name)) {
                return call_user_func_array([$helper, $name], $params);
            }
        }

        if (method_exists($this->pdf, $name)) {
            return call_user_func_array([$this->pdf, $name], $params);
        }

        throw new BadMethodCallException(sprintf(self::BAD_METHOD_EXCEPTION, $name));
    }

    abstract public function __toString(): string;
    abstract public function getAuthor(): string;
    abstract public function getCreator(): string;
    abstract public function getKeywords(): string;
    abstract public function getName(): string;
    abstract public function getPath(): string;
    abstract public function getSubject(): string;
    abstract public function getTitle(): string;
    abstract public function withAuthor(string $author): DocumentInterface;
    abstract public function withCreator(string $creator): DocumentInterface;
    abstract public function withKeywords(string ...$keywords): DocumentInterface;
    abstract public function withName(string $name): DocumentInterface;
    abstract public function withPath(string $path): DocumentInterface;
    abstract public function withSubject(string $subject): DocumentInterface;
    abstract public function withTitle(string $title): DocumentInterface;

    public function withHelpers(Helper ...$helpers): DocumentInterface
    {
        $new = clone $this;

        foreach ($helpers as $helper) {
            $helper->setDocument($new);
            $this->helpers[] = $helper;
        }

        return $new;
    }
}
