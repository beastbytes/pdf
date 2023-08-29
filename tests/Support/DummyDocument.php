<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests\Support;

use BeastBytes\PDF\Document;
use BeastBytes\PDF\DocumentInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use Yiisoft\ResponseDownload\DownloadResponseFactory;

final class DummyDocument extends Document
{
    private string $author = '';
    private string $creator = '';
    private array $customProperties = [];
    private string $keywords = '';
    private string $name = '';
    private string $path = '';
    private string $subject = '';
    private string $title = '';

    public function __construct()
    {
        $this->pdf = new DummyPdfLib();
    }

    public function __toString(): string
    {
        return $this
            ->pdf
            ->toString()
        ;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getCreator(): string
    {
        return $this->creator;
    }

    public function getCustomProperties(): array
    {
        return $this->customProperties;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function output(
        string $destination,
        DownloadResponseFactory $downloadResponseFactory
    ): bool|string|ResponseInterface {
        return (string)$this;
    }

    public function withAuthor(string $author): DocumentInterface
    {
        $new = clone $this;
        $new->author = $author;
        return $new;
    }

    public function withCreator(string $creator): DocumentInterface
    {
        $new = clone $this;
        $new->creator = $creator;
        return $new;
    }

    public function withCustomProperties(array $customProperties): DocumentInterface
    {
        $new = clone $this;
        $new->customProperties = $customProperties;
        return $new;
    }

    public function withKeywords(string ...$keywords): DocumentInterface
    {
        $new = clone $this;
        $new->keywords = implode(' ', $keywords);
        return $new;
    }

    public function withName(string $name): DocumentInterface
    {
        $new = clone $this;
        $new->name = $name;
        return $new;
    }

    public function withPath(string $path): DocumentInterface
    {
        $new = clone $this;
        $new->path = $path;
        return $new;
    }


    public function withSubject(string $subject): DocumentInterface
    {
        $new = clone $this;
        $new->subject = $subject;
        return $new;
    }

    public function withTitle(string $title): DocumentInterface
    {
        $new = clone $this;
        $new->title = $title;
        return $new;
    }
}
