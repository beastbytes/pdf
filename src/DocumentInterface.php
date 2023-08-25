<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF;

interface DocumentInterface
{
    /**
     * @return string Name of the entity (person, organisation, ...) that created the document
     */
    public function getAuthor(): string;

    /**
     * @return string Name of the package used to create the document
     */
    public function getCreator(): string;

    /**
     * @return array<string, int|string> Custom properties for the document
     */
    public function getCustomProperties(): array;

    /**
     * @return string Keywords for the document
     */
    public function getKeywords(): string;

    /**
     * @return string Name of the document when displayed in the browser, downloaded, or saved
     */
    public function getName(): string;

    /**
     * @return string Subject of the document
     */
    public function getSubject(): string;

    /**
     * @return string Document title
     */
    public function getTitle(): string;

    /**
     * @param string $author Name of the entity (person, organisation, ...) that created the document
     * @return self
     */
    public function withAuthor(string $author): self;

    /**
     * @param string $creator Name of the package used to create the document
     * @return self
     */
    public function withCreator(string $creator): self;

    /**
     * @param array<string, int|string> $customProperties Custom properties for the document
     * @return self
     */
    public function withCustomProperties(array $customProperties): self;

    /**
     * @param string ...$keywords Keywords for the document
     * @return self
     */
    public function withKeywords(string ...$keywords): self;

    /**
     * @param string $name Name of the document when displayed in the browser, downloaded, or saved
     * @return self
     */
    public function withName(string $name): self;

    /**
     * @param string $subject Subject of the document
     * @return self
     */
    public function withSubject(string $subject): self;

    /**
     * @param string $title Document title
     * @return self
     */
    public function withTitle(string $title): self;
}
