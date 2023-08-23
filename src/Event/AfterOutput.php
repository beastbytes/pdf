<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Event;

use BeastBytes\PDF\DocumentInterface;

/**
 * `AfterOutput` event is triggered right after outputting the document.
 */
final class AfterOutput
{
    public function __construct(private DocumentInterface $document)
    {
    }

    public function getDocument(): DocumentInterface
    {
        return $this->document;
    }
}
