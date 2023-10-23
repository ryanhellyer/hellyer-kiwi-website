<?php

declare(strict_types=1);

namespace Utils;

use Interfaces\ValidationInterface;
use Config\Config;

/**
 * Validation Class
 * 
 * Implements the ValidationInterface. Provides methods for validating
 * file hashes and POST data.
 */
class Validation implements ValidationInterface
{
    /**
     * Compares a file's stored hash with a given hash.
     *
     * @param string $path File path to validate.
     * @param string $hash Expected hash for validation.
     * @return bool True if the file hash matches, false otherwise.
     */
    public function checkHash(string $path, string $hash): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $contents = file_get_contents($path);
        $bits = explode(Config::SEPARATOR, $contents);
        $storedHash = $bits[0] ?? '';

        return $storedHash === $hash;
    }

    /**
     * Validates required keys in a POST data array.
     *
     * @param array $postData Data received via POST.
     * @param array $requiredKeys Keys expected in the POST data.
     * @return bool True if all required keys exist.
     * @throws \Exception when required parameters are missing.
     */
    public function validatePostData(array $postData, array $requiredKeys): array
    {
        $missingKeys = array_diff($requiredKeys, array_keys($postData));

        if ($missingKeys) {
            throw new \Exception('Missing required parameters: ' . implode(',', $missingKeys));
        }

        return true;
    }
}
