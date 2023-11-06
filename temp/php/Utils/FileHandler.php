<?php

declare(strict_types=1);

namespace Utils;

use Interfaces\FileHandlerInterface;

/**
 * FileHandler Class
 *
 * A utility class responsible for abstracting file system operations,
 * designed to facilitate easier testing of the Files class.
 *
 * Implements the FileHandlerInterface.
 */
class FileHandler implements FileHandlerInterface {

    /**
     * Writes data to a file.
     *
     * @param string $path The path where the file should be written to.
     * @param string $data The data to write to the file.
     * @return bool Returns true if the operation was successful, false otherwise.
     */
    public function putContents(string $path, string $data): bool
    {
        return file_put_contents($path, $data) !== false;
    }

    /**
     * Retrieves the contents of a file.
     *
     * @param string $path The path of the file to read.
     * @return string Returns the file contents. If the file does not exist, returns an empty string.
     */
    public function getContents(string $path): string
    {
        return file_get_contents($path) ?: '';
    }

    /**
     * Checks if a file exists.
     *
     * @param string $path The path of the file to check.
     * @return bool Returns true if the file exists, false otherwise.
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Deletes a file.
     *
     * @param string $path The path of the file to delete.
     * @return bool Returns true if the file was successfully deleted, false otherwise.
     */
    public function delete(string $path): bool
    {
        return unlink($path);
    }
}
