<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF;

interface DocumentFactoryInterface
{
    /**
     * Creates a new document instance.
     *
     * @return DocumentInterface The document instance.
     */
    public function create(): DocumentInterface;
}
