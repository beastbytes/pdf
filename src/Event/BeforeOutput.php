<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Event;

use BeastBytes\PDF\DocumentInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * `BeforeOutput` event is triggered right before outputting the document.
 */
final class BeforeOutput implements StoppableEventInterface
{
    private bool $stopPropagation = false;

    public function __construct(private DocumentInterface $document)
    {
    }

    public function getDocument(): DocumentInterface
    {
        return $this->document;
    }

    public function stopPropagation(): void
    {
        $this->stopPropagation = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopPropagation;
    }
}
