<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Helper;

use BeastBytes\PDF\DocumentInterface;

/**
 * Helper classes enable the application to use higher level methods in the view.
 */
abstract class Helper
{
    private DocumentInterface $document;

    public function getDocument(): DocumentInterface
    {
        return $this->document;
    }

    public function setDocument(DocumentInterface $document): void
    {
        $this->document = $document;
    }
}
