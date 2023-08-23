<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF;

use Yiisoft\View\ViewContextInterface;

/**
 * Stores the path to the view file directory.
 */
final class DocumentTemplate implements ViewContextInterface
{
    /**
     * @param string $viewPath The directory containing view files for generating PDF documents.
     */
    public function __construct(
        private string $viewPath
    ) {
    }

    /**
     * Returns the directory containing view files for composing mail documents.
     *
     * @return string The directory containing view files for composing mail documents.
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }
}
