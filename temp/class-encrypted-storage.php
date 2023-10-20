<?php

declare(strict_types=1);

/**
 * Class EncryptedFileManager
 * 
 * Manages encrypted files, including saving, deleting, and listing.
 */
class EncryptedStorage
{
    private const DIR_PATH = __DIR__ . '/encrypted/';
    private const SEPARATOR = '<<<SEPARATOR>>>';

	/**
     * Function to list encrypted files and generate HTML list items for each.
     *
     * @return string The HTML output containing list items for each encrypted file.
     */
    public function listEncryptedFiles(): string
    {
        $pages = [];
        $output = '';

        foreach (new DirectoryIterator(self::DIR_PATH) as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            if ($fileInfo->getExtension() === 'data') {
                $pages[] = $fileInfo->getBasename('.data');
            }
        }

        sort($pages);

        foreach ($pages as $page) {
            $path = self::DIR_PATH . $page . '.data';
            $contents = file_get_contents($path);
            if ($contents !== false) {
                $output .= $this->listItem(false, $page, $contents);
            }
        }

        $output .= $this->listItem(true);

        return $output;
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
	 * @throws Exception If an error occurs during the save operation.
	 */
	public function handleSaveRequest(array $postData): array
    {
        if (
            !isset($postData['title']) || 
            !isset($postData['originalTitle']) || 
            !isset($postData['encryptedContent']) || 
            !isset($postData['hash'])
        ) {
            http_response_code(400);
            return ['error' => 'Parameters not found'];
        }

        try {
            $response = $this->saveData($postData['title'], $postData['originalTitle'], $postData['encryptedContent'], $postData['hash']);
            return ['response' => $response];
        } catch (Exception $e) {
            return ['error' => $this->escHtml($e->getMessage())];
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
	 * @throws Exception If an error occurs during the delete operation.
	 */
    public function handleDeleteRequest(array $postData): array
    {
        if (!isset($postData['title']) || !isset($postData['hash'])) {
            http_response_code(400);
            return ['error' => 'Parameters not found'];
        }

        try {
            $response = $this->deleteItem($postData['title'], $postData['hash']);
            return ['response' => $response];
        } catch (Exception $e) {
            return ['error' => $this->escHtml($e->getMessage())];
        }
    }

    /**
     * Escapes HTML attributes to prevent XSS attacks.
     * 
     * @param string $attr The attribute to be escaped.
     * @return string The escaped attribute.
     */
    private function escAttr(string $attr): string
    {
        return htmlspecialchars($attr, ENT_QUOTES);
    }

    /**
     * Escapes HTML content to prevent XSS attacks.
     * 
     * @param string $html The HTML content to be escaped.
     * @return string The escaped HTML content.
     */
    private function escHtml(string $html): string
    {
        return htmlspecialchars($html);
    }

    /**
     * Generates an HTML list item for encrypted content.
     * 
     * @param bool $new Indicates if this is a new item. If true, additional CSS classes are added.
     * @param string $page The title of the page.
     * @param string $fileContent The encrypted content from the file.
     * @return string The generated HTML list item.
     */
    private function listItem(bool $new = false, string $page = '', string $fileContent = ''): string
    {
        $bits = explode(self::SEPARATOR, $fileContent);
        $hash = $bits[0];
        $encryptedContent = $bits[1] ?? $bits[0];
        $classes = $new ? ' class="decrypted new"' : '';

        return "
            <li{$classes}>
                <input type=\"text\" value=\"{$this->escHtml($page)}\">
                <input type=\"hidden\" value=\"{$this->escHtml($page)}\">
                <input type=\"password\" value=\"\" placeholder=\"Enter password\">
                <textarea>{$this->escHtml($encryptedContent)}</textarea>
                <button class=\"save\">Save</button>
                <button class=\"delete\">Delete</button>
                <p></p>
            </li>";
    }

    /**
     * Checks if the hash of the file matches with the previously saved hash.
     * 
     * @param string $path The path to the file.
     * @param string $hash The hash to validate.
     * @return bool True if the hashes match, false otherwise.
     */
    private function checkHash(string $path, string $hash): bool
    {
        if (!file_exists($path)) {
			return true;
		}

		$contents = file_get_contents($path);
        $bits = explode(self::SEPARATOR, $contents);
		$storedHash = $bits[0];

		return $storedHash === $hash;
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
    private function saveData(string $title, string $originalTitle, string $encryptedContent, string $hash): bool
    {
        $oldPath = self::DIR_PATH . $originalTitle . '.data';
        $newPath = self::DIR_PATH . $title . '.data';

		if (!$this->checkHash($newPath, $hash)) {
            throw new Exception('Password hashes do not match!');
        }

        if ($oldPath !== $newPath && file_exists($newPath)) {
            throw new Exception('That file name already exists');
        }

        $fileContent = $hash . self::SEPARATOR . $encryptedContent;

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
    private function deleteItem(string $title, string $hash): bool
    {
        $path = self::DIR_PATH . $title . '.data';

        if (!$this->checkHash($path, $hash)) {
            throw new Exception('Password hashes do not match!');
        }

        if (file_exists($path)) {
            return unlink($path);
        }

        return false;
    }
}

$encryptedStorage = new EncryptedStorage();

if (isset($_GET['save'])) {
	header('Content-Type: application/json');
    echo json_encode($encryptedStorage->handleSaveRequest($_POST));
    exit;
}

if (isset($_GET['delete'])) {
	header('Content-Type: application/json');
    echo json_encode($encryptedStorage->handleDeleteRequest($_POST));
    exit;
}
