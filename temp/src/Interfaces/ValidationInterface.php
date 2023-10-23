<?php

declare(strict_types=1);

namespace Interfaces;

/**
 * Validation Interface
 *
 * Provides methods for data and hash validation.
 */
interface ValidationInterface {

    /**
     * Validates a file's hash against a given hash.
     *
     * @param string $path Path to the file.
     * @param string $hash The hash to validate against.
     * @return bool True if the hashes match, false otherwise.
     */
    public function checkHash(string $path, string $hash): bool;

    /**
     * Validates the presence of required keys in POST data.
     *
     * @param array $postData Data received via POST.
     * @param array $requiredKeys Keys that must be present in postData.
     * @return array|bool True if all required keys are present, or an array containing missing keys otherwise.
     */
    public function validatePostData(array $postData, array $requiredKeys): array|bool;
}
