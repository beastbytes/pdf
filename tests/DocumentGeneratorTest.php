<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests;

use BeastBytes\PDF\DocumentGenerator;
use BeastBytes\PDF\DocumentTemplate;
use BeastBytes\PDF\Tests\Support\DummyDocument;
use BeastBytes\PDF\Tests\Support\TestCase;
use Yiisoft\View\View;

use const DIRECTORY_SEPARATOR;

class DocumentGeneratorTest extends TestCase
{
    private const TEST_LAYOUT_TEXT = "Begin Layout\n{content}\nEnd Layout";

    public function test_constructor(): void
    {
        $viewPath = self::getTestFilePath() . DIRECTORY_SEPARATOR . 'view';
        $template = new DocumentTemplate($viewPath);

        $this->assertSame($viewPath, $template->getViewPath());
    }

    public function testWithView(): void
    {
        $generator = $this->createGenerator(self::getTestFilePath());
        $view = clone $this->get(View::class);
        $newGenerator = $generator->withView($view);

        $this->assertNotSame($generator, $newGenerator);
        $this->assertSame($view, $this->getInaccessibleProperty($newGenerator, 'view'));
    }

    public function testWithTemplate(): void
    {
        $generator = $this->createGenerator(self::getTestFilePath());
        $template = new DocumentTemplate(self::getTestFilePath());
        $newGenerator = $generator->withTemplate($template);

        $this->assertNotSame($generator, $newGenerator);
        $this->assertSame($template, $this->getInaccessibleProperty($newGenerator, 'template'));
    }

    public function testGenerate(): void
    {
        $viewPath = self::getTestFilePath();
        $generator = $this
            ->createGenerator($viewPath)
        ;

        $viewName = 'test-view';
        $viewFileName = $viewPath . DIRECTORY_SEPARATOR . $viewName . '.php';
        $this->saveFile($viewFileName, "<?php\n" . '$document->setText("' . self::TEST_TEXT . '");');

        $document = new DummyDocument();
        $generator->generate($document, 'test-view');

        $this->assertSame(self::TEST_TEXT,  (string)$document);
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
        $this->saveFile($viewFileName, "<?php\n" . '$document->setText("' .self::TEST_LOCALE . ' locale");');

        $document = new DummyDocument();
        $generator->generate($document, 'test-view-locale');

        $this->assertSame(self::TEST_LOCALE . ' locale',  (string)$document);
    }

    public function createGenerator(string $viewPath): DocumentGenerator
    {
        return new DocumentGenerator(
            $this->get(View::class),
            new DocumentTemplate($viewPath)
        );
    }
}
