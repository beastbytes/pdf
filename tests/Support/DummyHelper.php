<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests\Support;

use BeastBytes\PDF\Helper\Helper;

class DummyHelper extends Helper
{
    public function writeHelperLine(string $text): void
    {
        $this
            ->getDocument()
            ->writeLine($text)
        ;
    }
}
