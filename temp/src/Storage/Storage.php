<?php

declare(strict_types=1);

namespace Storage;

use Interfaces\EscaperInterface;
use Interfaces\FilesInterface;
use Interfaces\ValidationInterface;
use Config\Config;

/**
 * Class EncryptedFileManager
 *
 * Manages encrypted files, including saving, deleting, and listing.
 */
class Storage
{
    private EscaperInterface $escaper;
    private FilesInterface $files;
    private ValidationInterface $validation;

    /**
     * Constructor.
     *
     * @param EscaperInterface $escaper
     * @param FilesInterface $files
     * @param ValidationInterface $validation
     */
    public function __construct(
        EscaperInterface $escaper,
        FilesInterface $files,
        ValidationInterface $validation
    ) {
        $this->escaper = $escaper;
        $this->files = $files;
        $this->validation = $validation;
    }

    /**
     * Handles the save request and returns an associative array as the response.
     *
     * This method validates the POST data and attempts to save the encrypted data
     * to a file. If successful, it returns an array with a 'response' key set to true.
     * If an error occurs, it returns an array with an 'error' key containing the error message.
     *
     * @param array $postData The POST data to be processed.
     * @return array The response array containing either the result or an error message.
     * @throws \Exception If an error occurs during the save operation.
     */
    public function handleSaveRequest(array $postData): array
    {
        $requiredKeys = ['title', 'originalTitle', 'encryptedContent', 'hash'];
        try {
            $response = $this->validation->validatePostData($postData, $requiredKeys);
            if ($response !== true) {
                http_response_code(400);
                return $response;
            }
        } catch (\Exception $e) {
            return ['error' => $this->escaper->escHtml($e->getMessage())];
        }

        try {
            $response = $this->files->saveData(
                $postData['title'],
                $postData['originalTitle'],
                $postData['encryptedContent'],
                $postData['hash']
            );
            return ['response' => $response];
        } catch (\Exception $e) {
            return ['error' => $this->escaper->escHtml($e->getMessage())];
        }
    }

    /**
     * Handles the delete request and returns an associative array as the response.
     *
     * This method validates the POST data and attempts to delete the encrypted file.
     * If successful, it returns an array with a 'response' key set to true.
     * If an error occurs, it returns an array with an 'error' key containing the error message.
     *
     * @param array $postData The POST data to be processed.
     * @return array The response array containing either the result or an error message.
     * @throws \Exception If an error occurs during the delete operation.
     */
    public function handleDeleteRequest(array $postData): array
    {
        $requiredKeys = ['title', 'hash'];
        try {
            $response = $this->validation->validatePostData($postData, $requiredKeys);
        } catch (\Exception $e) {
            http_response_code(400);
            return ['error' => $this->escaper->escHtml($e->getMessage())];
        }

        try {
            $response = $this->files->deleteItem($postData['title'], $postData['hash']);
            return ['response' => $response];
        } catch (\Exception $e) {
            http_response_code(400);
            return ['error' => $this->escaper->escHtml($e->getMessage())];
        }
    }
}
