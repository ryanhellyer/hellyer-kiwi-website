<?php

declare(strict_types=1);

namespace Interfaces;

/**
 * File Handler Interface
 *
 * Provides methods for basic file operations such as reading, writing,
 * checking existence, and deletion.
 */
interface FileHandlerInterface {
    
    /**
     * Writes data to a file at the specified path.
     *
     * @param string $path The file path where data should be written.
     * @param string $data The data to write to the file.
     * @return bool True if the operation was successful, false otherwise.
     */
    public function putContents(string $path, string $data): bool;

    /**
     * Reads the contents of a file at the specified path.
     *
     * @param string $path The file path to read from.
     * @return string The contents of the file.
     */
    public function getContents(string $path): string;

    /**
     * Checks if a file exists at the specified path.
     *
     * @param string $path The file path to check.
     * @return bool True if the file exists, false otherwise.
     */
    public function exists(string $path): bool;

    /**
     * Deletes a file at the specified path.
     *
     * @param string $path The file path to delete.
     * @return bool True if the operation was successful, false otherwise.
     */
    public function delete(string $path): bool;
}
