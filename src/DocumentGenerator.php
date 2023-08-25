<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF;

use Throwable;
use Yiisoft\View\View;
use Yiisoft\View\ViewContextInterface;

final class DocumentGenerator
{
    /**
     * @param View $view The view instance.
     * @param ViewContextInterface $viewContext The view context for generating PDF documents.
     */
    public function __construct(
        private View $view,
        private ViewContextInterface $viewContext
    ) {
    }

    /**
     * Generates the PDF document using view specified with optional parameters.
     *
     * @param DocumentInterface $document The document
     * @param string $view The view name of the view file.
     * @param array $viewParameters The parameters (name-value pairs)
     * that will be extracted and available in the view file.
     *
     * @throws Throwable If an error occurred during rendering.
     *
     * @see View::render()
     */
    public function generate(
        DocumentInterface $document,
        string $view,
        array $viewParameters = []
    ): void
    {
        $viewParameters['document'] = $document;
        $this->view
            ->withContext($this->viewContext)
            ->render($view, $viewParameters)
        ;
    }

    /**
     * Returns a new instance with the specified view.
     *
     * @param View $view The view instance.
     * @return self The new instance.
     */
    public function withView(View $view): self
    {
        $new = clone $this;
        $new->view = $view;
        return $new;
    }

    /**
     * Returns a new instance with the specified view context.
     *
     * @param ViewContextInterface $viewContext The view context for generating PDF documents.
     * @return self The new instance.
     */
    public function withViewContext(ViewContextInterface $viewContext): self
    {
        $new = clone $this;
        $new->viewContext = $viewContext;
        return $new;
    }

    /**
     * Returns a new instance with specified locale code.
     *
     * @param string $locale The locale code.
     * @return self The new instance.
     */
    public function withLocale(string $locale): self
    {
        $new = clone $this;
        $new->view = $this
            ->view
            ->withLocale($locale)
        ;
        return $new;
    }
}
