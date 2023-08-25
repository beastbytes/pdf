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
use BeastBytes\PDF\PdfInterface;
use BeastBytes\PDF\Tests\Support\DummyDocument;
use BeastBytes\PDF\Tests\Support\DummyPdf;
use BeastBytes\PDF\Tests\Support\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
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

        $this->assertNotSame($viewContext, $this->getInaccessibleProperty($oldDocumentGenerator, 'viewContext'));
        $this->assertSame($viewContext, $this->getInaccessibleProperty($newDocumentGenerator, 'viewContext'));
    }

    public function testGenerate(): void
    {
        $pdf = $this->get(PdfInterface::class);
        $viewPath = self::getTestFilePath();

        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, "<?php\n" . '$document->setText("' . self::TEST_TEXT . '");');

        $document = $pdf->generate($viewName);
        $this->assertSame(self::TEST_TEXT, (string)$document);
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
            "<?php\n" . '$document->setText("' . self::TEST_LOCALE . ' ' . self::TEST_TEXT . '");'
        );

        $document = $pdf
            ->withLocale('de_DE')
            ->generate($viewName)
        ;
        $this->assertSame(self::TEST_LOCALE . ' ' . self::TEST_TEXT, (string)$document);
    }

    public function testBeforeOutput(): void
    {
        $document = new DummyDocument();
        $event = new BeforeOutput($document);
        $documentFactory = $this->get(DocumentFactoryInterface::class);
        $documentGenerator = $this->get(DocumentGenerator::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->method('dispatch')
            ->willReturn($event)
        ;
        $pdf = new DummyPdf($documentFactory, $documentGenerator, $eventDispatcher);

        $this->assertTrue($pdf->beforeOutput($document));
        $event->stopPropagation();
        $this->assertFalse($pdf->beforeOutput($document));
    }

    public function testAfterOutput(): void
    {
        $pdf = $this->get(PdfInterface::class);
        $viewPath = self::getTestFilePath();

        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, "<?php\n" . '$document->setText("' . self::TEST_TEXT . '");');

        $document = $pdf->generate($viewName);
        $pdf->afterOutput($document);

        $eventClasses = $this
            ->get(EventDispatcherInterface::class)
            ->getEventClasses()
        ;

        $this->assertSame(AfterOutput::class, array_pop($eventClasses));
    }

    public function testImmutability(): void
    {
        $pdf = $this->get(PdfInterface::class);

        $this->assertNotSame($pdf, $pdf->withLocale('de_DE'));
    }
}
