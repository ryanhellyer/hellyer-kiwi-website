<?php

declare(strict_types=1);

namespace Utils;

use Interfaces\ValidationInterface;
use Config\Config;

/**
 * Class Validation
 *
 * Utility class for Validation.
 */
class Validation implements ValidationInterface
{
    /**
     * Checks if the hash of the file matches with the previously saved hash.
     *
     * @param string $path The path to the file.
     * @param string $hash The hash to validate.
     * @return bool True if the hashes match, false otherwise.
     */
    public function checkHash(string $path, string $hash): bool
    {
        if (!file_exists($path)) {
            return true;
        }

        $contents = file_get_contents($path);
        $bits = explode(Config::SEPARATOR, $contents);
        $storedHash = $bits[0];

        return $storedHash === $hash;
    }

    /**
     * Validates the presence of required keys in the POST data array.
     *
     * @param array $postData The array containing POST data.
     * @param array $requiredKeys An array of keys to check for in the POST data.
     *
     * @return array|bool Returns true if all required keys are present.
     *                    Returns an array of missing keys if any are absent.
     */
    public function validatePostData(array $postData, array $requiredKeys): array|bool
    {
        $missingKeys = [];

        foreach ($requiredKeys as $key) {
            if (!isset($postData[$key])) {
                $missingKeys[] = $key;
            }
        }

        if (! empty($missingKeys)) {
            $missingKeysString = implode(',', $missingKeys);
            return ['error' => 'These required parameters were not found: ' . $missingKeysString];
        }

        return true;
    }
}
