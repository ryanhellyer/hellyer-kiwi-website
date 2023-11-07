<?php

declare(strict_types=1);

namespace Interfaces;

/**
 * Storage Interface
 *
 * @todo.
 */
interface StorageInterface {

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
    public function handleSaveRequest(array $postData): array;

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
    public function handleDeleteRequest(array $postData): array;

}
