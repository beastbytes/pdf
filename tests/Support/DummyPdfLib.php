<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests\Support;

class DummyPdfLib
{
    private string $content = '';

    public function toString(): string
    {
        return $this->content;
    }

    public function writeLine(string $text): void
    {
        $this->content .= "$text\n";
    }
}
