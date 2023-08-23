<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF;

use Throwable;
use Yiisoft\View\View;

final class DocumentGenerator
{
    /**
     * @param View $view The view instance.
     * @param DocumentTemplate $template The document template instance.
     */
    public function __construct(
        private View $view,
        private DocumentTemplate $template
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
            ->withContext($this->template)
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
     * Returns a new instance with the specified document template.
     *
     * @param DocumentTemplate $template The document template.
     * @return self The new instance.
     */
    public function withTemplate(DocumentTemplate $template): self
    {
        $new = clone $this;
        $new->template = $template;
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
