<?php

declare(strict_types=1);

namespace Interfaces;

/**
 * Files Interface
 *
 * Provides methods for managing document files, including saving, deleting,
 * and retrieving documents and templates.
 */
interface FilesInterface {
    
    /**
     * Saves data to a document file.
     *
     * @param string $title Title used to identify the document.
     * @param string $originalTitle Original title of the document.
     * @param string $encryptedContent Encrypted content to be saved.
     * @param string $hash Hash associated with the document.
     * @return bool True if the operation was successful, false otherwise.
     */
    public function saveData(string $title, string $originalTitle, string $encryptedContent, string $hash, string $textContent/*@todo remove textContent*/): bool;

    /**
     * Deletes a document item.
     *
     * @param string $title Title used to identify the document.
     * @param string $hash Hash associated with the document.
     * @return bool True if the operation was successful, false otherwise.
     */
    public function deleteItem(string $title, string $hash): bool;

    /**
     * Retrieves all document details.
     *
     * @return array Array of document details.
     */
    public function retrieveDocs(): array;

    /**
     * Retrieves the content of a specific document.
     *
     * @param string $doc Title or identifier of the document.
     * @return array Returns an array of document content.
     */
    public function retrieveDocContent(string $doc): array;

    /**
     * Retrieves the content of a template.
     *
     * @param string $templatePath The file path of the template to retrieve.
     * @return string The content of the template.
     */
    public function retrieveTemplate(string $templatePath): string;
}
