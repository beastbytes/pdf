<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests;

use BeastBytes\PDF\DocumentFactoryInterface;
use BeastBytes\PDF\DocumentGenerator;
use BeastBytes\PDF\Event\AfterOutput;
use BeastBytes\PDF\Event\BeforeOutput;
use BeastBytes\PDF\Pdf;
use BeastBytes\PDF\PdfInterface;
use BeastBytes\PDF\Tests\Support\DummyDocument;
use BeastBytes\PDF\Tests\Support\DummyPdf;
use BeastBytes\PDF\Tests\Support\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\ResponseDownload\DownloadResponseFactory;
use Yiisoft\View\ViewContext;

use const DIRECTORY_SEPARATOR;

class PdfTest extends TestCase
{
    public function testWithViewContext(): void
    {
        $pdf = $this->get(PdfInterface::class);
        $viewContext = new ViewContext(self::getTestFilePath(), '', '');

        $oldDocumentGenerator = $this->getInaccessibleProperty($pdf, 'documentGenerator');
        $newPdf = $pdf->withViewContext($viewContext);
        $newDocumentGenerator = $this->getInaccessibleProperty($newPdf, 'documentGenerator');

        $this->assertNotSame($pdf, $newPdf);
        $this->assertNotSame($oldDocumentGenerator, $newDocumentGenerator);

        $this->assertNotSame(
            $viewContext,
            $this->getInaccessibleProperty($oldDocumentGenerator, 'viewContext')
        );
        $this->assertSame(
            $viewContext,
            $this->getInaccessibleProperty($newDocumentGenerator, 'viewContext')
        );
    }

    public function testGenerate(): void
    {
        $pdf = $this->get(PdfInterface::class);
        $viewPath = self::getTestFilePath();

        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, "<?php\n" . '$document->writeLine("' . self::TEST_TEXT . '");');

        $document = $pdf->generate($viewName);
        $this->assertSame(self::TEST_TEXT . "\n", (string)$document);
    }

    public function testGenerateWithLocale(): void
    {
        $pdf = $this->get(PdfInterface::class);
        $viewPath = self::getTestFilePath();

        $viewName = 'test-view';
        $this->saveFile(
            $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php',
            "<?php\n" . '$document->setText("' . self::TEST_TEXT . '");'
        );

        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . self::TEST_LOCALE . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile(
            $viewFileName,
            "<?php\n" . '$document->writeLine("' . self::TEST_LOCALE . ' ' . self::TEST_TEXT . '");'
        );

        $document = $pdf
            ->withLocale('de_DE')
            ->generate($viewName)
        ;
        $this->assertSame(self::TEST_LOCALE . ' ' . self::TEST_TEXT . "\n", (string)$document);
    }

    public function testBeforeOutput(): void
    {
        $pdf = $this->get(PdfInterface::class);
        $viewPath = self::getTestFilePath();

        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, "<?php\n" . '$document->writeLine("' . self::TEST_TEXT . '");');

        $document = $pdf->generate($viewName);
        $pdf->output($document, 'S');

        $eventClasses = $this
            ->get(EventDispatcherInterface::class)
            ->getEventClasses()
        ;

        $this->assertSame(BeforeOutput::class, $eventClasses[2]);
    }

    public function testAfterOutput(): void
    {
        $pdf = $this->get(PdfInterface::class);
        $viewPath = self::getTestFilePath();

        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, "<?php\n" . '$document->writeLine("' . self::TEST_TEXT . '");');

        $document = $pdf->generate($viewName);
        $pdf->output($document, 'S');

        $eventClasses = $this
            ->get(EventDispatcherInterface::class)
            ->getEventClasses()
        ;

        $this->assertSame(AfterOutput::class, $eventClasses[3]);
    }

    public function testOutput(): void
    {
        $pdf = $this->get(PdfInterface::class);
        $viewPath = self::getTestFilePath();

        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, "<?php\n" . '$document->writeLine("' . self::TEST_TEXT . '");');

        $document = $pdf->generate($viewName);

        $this->assertSame(self::TEST_TEXT . "\n", $pdf->output($document, 'S'));
    }

    public function testImmutability(): void
    {
        $pdf = $this->get(PdfInterface::class);

        $this->assertNotSame($pdf, $pdf->withLocale('de_DE'));
    }

    private function createProvider(): Provider
    {
        return new Provider((new ListenerCollection())
            ->add([$this, 'afterOutput'], AfterOutput::class)
            ->add([$this, 'beforeOutput'], BeforeOutput::class)
        );
    }
}
