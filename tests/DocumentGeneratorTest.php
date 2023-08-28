<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests;

use BadMethodCallException;
use BeastBytes\PDF\Document;
use BeastBytes\PDF\DocumentGenerator;
use BeastBytes\PDF\Tests\Support\DummyDocument;
use BeastBytes\PDF\Tests\Support\DummyHelper;
use BeastBytes\PDF\Tests\Support\TestCase;
use Yiisoft\View\View;
use Yiisoft\View\ViewContext;

use const DIRECTORY_SEPARATOR;

class DocumentGeneratorTest extends TestCase
{
    private const TEST_LAYOUT_TEXT = "Begin Layout\n{content}\nEnd Layout";

    public function testWithView(): void
    {
        $generator = $this->createGenerator(self::getTestFilePath());
        $view = clone $this->get(View::class);
        $newGenerator = $generator->withView($view);

        $this->assertNotSame($generator, $newGenerator);
        $this->assertSame($view, $this->getInaccessibleProperty($newGenerator, 'view'));
    }

    public function testWithViewContext(): void
    {
        $generator = $this->createGenerator(self::getTestFilePath());
        $viewContext = new ViewContext(self::getTestFilePath());
        $newGenerator = $generator->withViewContext($viewContext);

        $this->assertNotSame($generator, $newGenerator);
        $this->assertSame($viewContext, $this->getInaccessibleProperty($newGenerator, 'viewContext'));
    }

    public function testGenerate(): void
    {
        $viewPath = self::getTestFilePath();
        $generator = $this
            ->createGenerator($viewPath)
        ;

        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, "<?php\n" . '$document->writeLine("' . self::TEST_TEXT . '");');

        $document = new DummyDocument();
        $generator->generate($document, 'test-view');

        $this->assertSame(self::TEST_TEXT . "\n",  (string)$document);
    }

    public function testWithHelper(): void
    {
        $viewPath = self::getTestFilePath();
        $generator = $this
            ->createGenerator($viewPath)
        ;

        $helperText = 'Helper ' . self::TEST_TEXT;
        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile(
            $viewFileName,
            "<?php\n"
            . '$document->writeLine("' . self::TEST_TEXT . '");'
            . '$document->writeHelperLine("' . $helperText . '");'
        );

        $document = new DummyDocument();
        $document->withHelpers(new DummyHelper());
        $generator->generate($document, 'test-view');

        $this->assertSame(self::TEST_TEXT . "\n$helperText\n",  (string)$document);
    }

    public function testWithLocale(): void
    {
        $viewPath = self::getTestFilePath();
        $generator = $this
            ->createGenerator($viewPath)
            ->withLocale(self::TEST_LOCALE)
        ;

        $viewName = 'test-view-locale';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, 'not localized');

        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . self::TEST_LOCALE . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, "<?php\n" . '$document->writeLine("' .self::TEST_LOCALE . ' locale");');

        $document = new DummyDocument();
        $generator->generate($document, 'test-view-locale');

        $this->assertSame(self::TEST_LOCALE . " locale\n",  (string)$document);
    }

    public function testMethodDoesNotExist(): void
    {
        $viewPath = self::getTestFilePath();
        $generator = $this
            ->createGenerator($viewPath)
        ;

        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile(
            $viewFileName,
            "<?php\n"
            . '$document->feedDragons("' . self::TEST_TEXT . '");'
        );

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(sprintf(Document::BAD_METHOD_EXCEPTION, 'feedDragons'));

        $document = new DummyDocument();
        $document->withHelpers(new DummyHelper());
        $generator->generate($document, 'test-view');
    }

    public function createGenerator(string $viewPath): DocumentGenerator
    {
        return new DocumentGenerator(
            $this->get(View::class),
            new ViewContext($viewPath)
        );
    }
}
