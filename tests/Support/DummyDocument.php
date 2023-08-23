<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\Tests\Support;

use BeastBytes\PDF\Document;
use BeastBytes\PDF\DocumentInterface;
use stdClass;

class DummyDocument extends Document
{
    private string $author = '';
    private string $creator = '';
    private array $customProperties = [];
    private array $keywords = [];
    private string $name = '';
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

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getTitle(): string
    {
        return $this->title;
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
        $new->keywords = $keywords;
        return $new;
    }

    public function withName(string $name): DocumentInterface
    {
        $new = clone $this;
        $new->name = $name;
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
