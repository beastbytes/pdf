<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests\Support;

use BeastBytes\PDF\Pdf;
use BeastBytes\PDF\DocumentInterface;
use InvalidArgumentException;

class DummyPdf extends Pdf
{
    public function output(
        DocumentInterface $document,
        string $destination = Pdf::DESTINATION_INLINE
    ): bool|string
    {
        return (string)$document;
    }

    public function beforeOutput(DocumentInterface $document): bool
    {
        return parent::beforeOutput($document);
    }

    public function afterOutput(DocumentInterface $document): void
    {
        parent::afterOutput($document);
    }
}