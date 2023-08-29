<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF;

use BeastBytes\PDF\Event\AfterOutput;
use BeastBytes\PDF\Event\BeforeOutput;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\ResponseDownload\DownloadResponseFactory;
use Yiisoft\View\ViewContextInterface;

/**
 * Generate a PDF document.
 */
abstract class Pdf implements PdfInterface
{
    public function __construct(
        private DocumentFactoryInterface $documentFactory,
        private DocumentGenerator $documentGenerator,
        private EventDispatcherInterface $eventDispatcher,
        private DownloadResponseFactory $downloadResponseFactory
    )
    {
    }

    /**
     * Returns a new instance with the specified view context.
     *
     * @param ViewContextInterface $viewContext The document template instance.
     * @return self The new instance.
     */
    public function withViewContext(ViewContextInterface $viewContext): self
    {
        $new = clone $this;
        $new->documentGenerator = $new
            ->documentGenerator
            ->withViewContext($viewContext)
        ;
        return $new;
    }

    /**
     * Returns a new instance with specified locale code.
     *
     * @param string $locale The locale code.
     * @return self
     */
    public function withLocale(string $locale): self
    {
        $new = clone $this;
        $new->documentGenerator = $new
            ->documentGenerator
            ->withLocale($locale)
        ;
        return $new;
    }

    public function generate($view, array $viewParameters = []): DocumentInterface
    {
        $document = $this->createDocument();

        $this
            ->documentGenerator
            ->generate($document, $view, $viewParameters)
        ;

        return $document;
    }

    /**
     * Output and/or save the document.
     *
     * @param DocumentInterface $document The document instance.
     * @param string $destination Where to send the document.
     * @return mixed
     */
    public function output(DocumentInterface $document, string $destination): mixed
    {
        if (!$this->beforeOutput($document)) {
            return false;
        }

        $return = $this->outputDocument($document, $destination);

        $this->afterOutput($document);

        return $return;
    }

    /**
     * Creates a new document instance.
     *
     * @return DocumentInterface The document instance.
     */
    protected function createDocument(): DocumentInterface
    {
        return $this->documentFactory->create();
    }

    /**
     * This method is invoked right before outputting the document.
     * Override this method to decide whether to output the document, calling the parent implementation first.
     *
     * @param DocumentInterface $document The document instance.
     * @return bool Whether to output the document.
     */
    protected function beforeOutput(DocumentInterface $document): bool
    {
        /** @var BeforeOutput $event */
        $event = $this
            ->eventDispatcher
            ->dispatch(new BeforeOutput($document))
        ;
        return !$event->isPropagationStopped();
    }

    /**
     * This method is invoked right after outputting the document.
     * Override this method to do logging, calling the parent implementation first.
     *
     * @param DocumentInterface $document The document instance.
     */
    protected function afterOutput(DocumentInterface $document): void
    {
        $this
            ->eventDispatcher
            ->dispatch(new AfterOutput($document))
        ;
    }

    /**
     * Outputs the document.
     *
     * @param DocumentInterface $document The document instance.
     * @param string $destination Where to send the document.
     * @param DownloadResponseFactory $downloadResponseFactory
     * @return bool|string|ResponseInterface ResponseInterface if sent to browser, string, or bool if saved only
     */
    abstract protected function outputDocument(
        DocumentInterface $document,
        string $destination,
        DownloadResponseFactory $downloadResponseFactory
    ): bool|string|ResponseInterface;
}
