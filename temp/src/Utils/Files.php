<?php

declare(strict_types=1);

namespace Utils;

use Interfaces\FilesInterface;
use Interfaces\ValidationInterface;
use Config\Config;

/**
 * Class Files
 *
 * Utility class for File management.
 */
class Files implements FilesInterface
{
    private $validation;

    /**
     * Constructor.
     *
     * @param ValidationInterface $validation An instance of the validation interface.
     */
    public function __construct(ValidationInterface $validation)
    {
        $this->validation = $validation;
    }

    /**
     * Saves encrypted data to a file.
     *
     * @param string $title The title of the file.
     * @param string $originalTitle The original title of the file.
     * @param string $encryptedContent The encrypted content to save.
     * @param string $hash The hash to validate.
     * @return bool True on success, false otherwise.
     * @throws Exception If an error occurs.
     */
    public function saveData(string $title, string $originalTitle, string $encryptedContent, string $hash): bool
    {
        $oldPath = Config::DIR_PATH . $originalTitle . '.' . Config::STORAGE_FILE_EXTENSION;
        $newPath = Config::DIR_PATH . $title . '.' . Config::STORAGE_FILE_EXTENSION;

        if (!$this->validation->checkHash($newPath, $hash)) {
            throw new Exception('Password hashes do not match!');
        }

        if ($oldPath !== $newPath && file_exists($newPath)) {
            throw new Exception('That file name already exists');
        }

        $fileContent = $hash . Config::SEPARATOR . $encryptedContent;

        if (file_put_contents($newPath, $fileContent) === false) {
            throw new Exception('File did not write');
        }

        if ('' !== $originalTitle && $oldPath !== $newPath && file_exists($oldPath)) {
            unlink($oldPath);
        }

        return true;
    }

    /**
     * Delete a file.
     *
     * @param string $title The title of the file.
     * @param string $hash The hash to validate.
     * @return bool True on success, false otherwise.
     * @throws Exception If an error occurs.
     */
    public function deleteItem(string $title, string $hash): bool
    {
        $path = Config::DIR_PATH . $title . '.' . Config::STORAGE_FILE_EXTENSION;

        if (!$this->validation->checkHash($path, $hash)) {
            throw new Exception('Password hashes do not match!');
        }

        if (file_exists($path)) {
            return unlink($path);
        }

        return false;
    }

    /**
     * Retrieves document filenames from a specified directory and sorts them.
     *
     * This method iterates over the files in the specified directory.
     * It filters out dot files and collects filenames with the required extension,
     * then sorts them alphabetically.
     *
     * @return array An array of sorted document filenames.
     */
    public function retrieveDocs(): array
    {
        $docs = [];
        foreach (new \DirectoryIterator(Config::DIR_PATH) as $docInfo) {
            if ($docInfo->isDot()) {
                continue;
            }
            if ($docInfo->getExtension() === Config::STORAGE_FILE_EXTENSION) {
                $docs[] = $docInfo->getBasename('.' . Config::STORAGE_FILE_EXTENSION);
            }
        }

        sort($docs);

        return $docs;
    }

    /**
     * Retrieves the content of a specified document file.
     *
     * @param string $doc The name of the document without the file extension.
     * @return array The hash and encrypted content in the document file.
     * @throws Exception If the file is not found.
     */
    public function retrieveDocContent(string $doc): array
    {
        $path = Config::DIR_PATH . $doc . '.' . Config::STORAGE_FILE_EXTENSION;
        if (false === $fileContent = file_get_contents($path)) {
            throw new Exception('File not found');
        }

        $bits = explode(Config::SEPARATOR, $fileContent);
        $hash = $bits[0];
        $encryptedContent = $bits[1] ?? $bits[0];

        return [
            'hash' => $hash,
            'encryptedContent' => $encryptedContent,
        ];
    }

    /**
     * Retrieves the content of a template file.
     *
     * Reads the template file specified by the given path. Throws an exception if the file is not found.
     * Returns the content of the file as a string.
     *
     * @param string $templatePath The path to the template file, provided by Config::ITEM_TEMPLATE_PATH.
     * @return string The content of the template file.
     * @throws \RuntimeException If the template file is not found.
     */
    public function retrieveTemplate(string $templatePath): string
    {
        $template = file_get_contents($templatePath);

        // Error handling for when no file is found.
        if ($template === false) {
            throw new \RuntimeException('Item template file not found.');
        }

        return $template;
    }

}
