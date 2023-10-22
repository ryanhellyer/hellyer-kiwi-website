<?php

declare(strict_types=1);

namespace Interfaces;

interface FilesInterface
{
    public function saveData(string $title, string $originalTitle, string $encryptedContent, string $hash): bool;
    public function deleteItem(string $title, string $hash): bool;
    public function retrieveDocs(): array;
    public function retrieveDocContent(string $doc): array;
    public function retrieveTemplate(string $templatePath): string;
}
