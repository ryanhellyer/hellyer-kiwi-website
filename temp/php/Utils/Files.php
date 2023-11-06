<?php

declare(strict_types=1);

namespace Utils;

use Interfaces\FilesInterface;
use Interfaces\ValidationInterface;
use Interfaces\FileHandlerInterface;
use Config\Config;

/**
 * Class Files
 *
 * Utility class for File management.
 */
class Files implements FilesInterface
{
    private ValidationInterface $validation;
    private FileHandlerInterface $fileHandler;

    /**
     * Constructor.
     *
     * @param ValidationInterface $validation An instance of the validation interface.
     * @param FileHandlerInterface $fileHandler An instance of the fileHandler interface.
     */
    public function __construct(ValidationInterface $validation, FileHandlerInterface $fileHandler)
    {
        $this->validation = $validation;
        $this->fileHandler = $fileHandler;
    }

    /**
     * Saves encrypted data to a file.
     *
     * @param string $title The title of the file.
     * @param string $originalTitle The original title of the file.
     * @param string $encryptedContent The encrypted content to save.
     * @param string $hash The hash to validate.
     * @return bool True on success, false otherwise.
     * @throws \Exception If an error occurs.
     */
    public function saveData(string $title, string $originalTitle, string $encryptedContent, string $hash, string $textContent/*@todo remove textContent*/): bool
    {
        $oldPath = Config::STORAGE_PATH . $originalTitle . '.' . Config::STORAGE_FILE_EXTENSION;
        $newPath = Config::STORAGE_PATH . $title . '.' . Config::STORAGE_FILE_EXTENSION;

        if (!$this->validation->checkHash($newPath, $hash)) {
            throw new \Exception('Password hashes do not match!');
        }

        if ($oldPath !== $newPath && $this->fileHandler->exists($newPath)) {
            throw new \Exception('That file name already exists');
        }
//@todo remove references to $textContent here.
        $fileContent = $hash . Config::SEPARATOR . $encryptedContent . Config::SEPARATOR . $textContent;
        $result = $this->fileHandler->putContents($newPath, $fileContent);

        if (!$result) {
            throw new \Exception('File did not write');
        }

        if (
            '' !== $originalTitle
            &&
            $oldPath !== $newPath
            &&
            $this->fileHandler->exists($oldPath)
        ) {
            $this->fileHandler->delete($oldPath);
        }

        return true;
    }

    /**
     * Delete a file.
     *
     * @param string $title The title of the file.
     * @param string $hash The hash to validate.
     * @return bool True on success, false otherwise.
     * @throws \Exception If an error occurs.
     */
    public function deleteItem(string $title, string $hash): bool
    {
        $path = Config::STORAGE_PATH . $title . '.' . Config::STORAGE_FILE_EXTENSION;

        if (!$this->validation->checkHash($path, $hash)) {
            throw new \Exception('Password incorrect!');
        }

        if (file_exists($path)) {
            return unlink($path); // Returns true if file successfully deleted, otherwise false.
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
        foreach (new \DirectoryIterator(Config::STORAGE_PATH) as $docInfo) {
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
     * @throws \Exception If the file is not found.
     */
    public function retrieveDocContent(string $doc): array
    {
        $path = Config::STORAGE_PATH . $doc . '.' . Config::STORAGE_FILE_EXTENSION;

        if (
            ! file_exists($path)
            ||
            false === $fileContent = file_get_contents($path)
        ) {
            throw new \Exception('File not found');
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
     * @throws \Exception If the template file is not found.
     */
    public function retrieveTemplate(string $templatePath): string
    {
        $template = file_get_contents($templatePath);

        // Error handling for when no file is found.
        if ($template === false) {
            throw new \Exception('Item template file not found.');
        }

        return $template;
    }
}
