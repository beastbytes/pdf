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

use function str_replace;

/**
 * Generate a PDF document.
 */
abstract class Pdf implements PdfInterface
{
    // Output destination
    /**
     * @var string Send to browser for download
     */
    public const DESTINATION_DOWNLOAD = 'D';
    /**
     * @var string Write to file
     */
    public const DESTINATION_FILE = 'F';
    /**
     * @var string Send to browser for inline display
     */
    public const DESTINATION_INLINE = 'I';
    /**
     * @var string Output a raw PDF string
     */
    public const DESTINATION_STRING = 'S';

    public function __construct(
        private DocumentFactoryInterface $documentFactory,
        private DocumentGenerator $documentGenerator,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }

    /**
     * Returns a new instance with the specified document template.
     *
     * @param DocumentTemplate $template The document template instance.
     * @return self The new instance.
     */
    public function withTemplate(DocumentTemplate $template): self
    {
        $new = clone $this;
        $new->documentGenerator = $new
            ->documentGenerator
            ->withTemplate($template)
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
     * Output and/or save the document
     *
     * @param DocumentInterface $document The document to output
     * @param string $destination Where to send the document.
     * It can be one of the following:
     * * D - send to the browser and force a file download with the name given by $name
     * * F - save to a local file with the name given by $name and path by $pdfPath - this option may be used with the
     * others, e.g. FI saves the document to a local file and sends it inline to the browser
     * * I - send the file inline to the browser; the PDF viewer is used if available - default
     * * S - return the document as a string
     *
     * @return bool|string The output result: Response if sent to the browser, raw PDF string, true is saved
     *   only, or false if not output
     * @throws InvalidConfigException
     */
    public function output(
        DocumentInterface $document,
        string $destination = self::DESTINATION_INLINE
    ): bool|string
    {
        if (!$this->beforeOutput($document)) {
            return false;
        }

        $return = false;

        $this->afterOutput($document);

        return $return;
    }

    /*
        if (str_contains($destination, self::DESTINATION_FILE)) {
            $destination = str_replace(self::DESTINATION_FILE, '', $destination);
            $return = file_put_contents(
                    Yii::getAlias($this->pdfPath) . DIRECTORY_SEPARATOR . $this->getName(),
                    $this->getString()
                ) !== false;
        }

        switch ($destination) {
            case self::DESTINATION_DOWNLOAD:
                $response = Yii::$app->response;
                $response->format = ResponseFormatter::PDF_DOWNLOAD;
                $response->data['name'] = $this->getName();
                $response->content = $this->getString();
                return $response;
            case self::DESTINATION_INLINE:
                $response = Yii::$app->response;
                $response->format = ResponseFormatter::PDF_INLINE;
                $response->data['name'] = $this->getName();
                $response->content = $this->getString();
                return $response;
            case self::DESTINATION_STRING:
                return $this->getString();
            default:
                return $return;
        }
     */

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
}
