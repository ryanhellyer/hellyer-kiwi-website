<?php

declare(strict_types=1);

/**
 * escAttr
 * 
 * Escapes HTML attributes to prevent XSS attacks.
 * 
 * @param string $attr The attribute to be escaped.
 * @return string The escaped attribute.
 */
function escAttr(string $attr): string {
    return htmlspecialchars($attr, ENT_QUOTES);
}

/**
 * escHtml
 * 
 * Escapes HTML content to prevent XSS attacks.
 * 
 * @param string $html The HTML content to be escaped.
 * @return string The escaped HTML content.
 */
function escHtml(string $html): string {
    return htmlspecialchars($html);
}

/**
 * listItem
 * 
 * Generates an HTML list item for encrypted content. The list item includes
 * input fields for the title, a hidden field for the original title, a password field,
 * and a textarea for the encrypted content.
 * 
 * @param bool $new Indicates if this is a new item. If true, additional CSS classes are added.
 * @param string $page The title of the page.
 * @param string $fileContent The encrypted content from the file.
 * @return string The generated HTML list item.
 */
function listItem(bool $new = false, string $page = '', string $fileContent = ''): string {
    // Split the file content into hash and encrypted content
    $bits = explode(SEPERATOR, $fileContent);

    // Extract the hash and encrypted content
    $hash = $bits[0];
    $encryptedContent = $bits[0]; // @todo REMOVE LATER, COZ SHOULDN'T DO THIS.
    if (isset($bits[1])) {
        $encryptedContent = $bits[1];
    }

    // Determine CSS classes
    $classes = '';
    if ($new === true) {
        $classes = ' class="decrypted new"';
    }

    // Generate the HTML list item
    return '
        <li' . $classes . '>
            <input type="text" value="' . escHtml($page) . '">
            <input type="hidden" value="' . escHtml($page) . '">
            <input type="password" value="" placeholder="Enter password">
            <textarea>' . escHtml($encryptedContent) . '</textarea>
            <button class="save">Save</button>
            <button class="delete">Delete</button>
            <p></p>
        </li>';
}

/**
 * Checks if the hash of the file matches with the previously saved hash.
 */
function checkHash(string $path, string $hash): bool {
    if (!file_exists($path)) return true;
    $contents = file_get_contents($path);
    $bits = explode(SEPERATOR, $contents);
    return $bits[0] === $hash;
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
function saveData(string $title, string $originalTitle, string $encryptedContent, string $hash): bool {
    $dirPath = DIR_PATH;
    $oldPath = $dirPath . $originalTitle . '.data';
    $newPath = $dirPath . $title . '.data';

    if (!checkHash($newPath, $hash)) {
        throw new Exception('Password hashes do not match!');
    }

    if ($oldPath !== $newPath && file_exists($newPath)) {
        throw new Exception('That file name already exists');
    }

    $fileContent = $hash . SEPERATOR . $encryptedContent;

    if (file_put_contents($newPath, $fileContent) === false) {
        throw new Exception('File did not write');
    }

    if ($oldPath !== $newPath) {
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
function deleteItem(string $title, string $hash): bool {
    $dirPath = DIR_PATH;
    $path = $dirPath . $title . '.data';

    if (!checkHash($path, $hash)) {
        throw new Exception('Password hashes do not match!');
    }

    return unlink($path);
}

/**
 * Function to list encrypted files and generate HTML list items for each.
 *
 * @return string The HTML output containing list items for each encrypted file.
 */
function listEncryptedFiles(): string {
    // Initialize directory path and an empty array to hold page names
    $dirPath = dirname(__FILE__) . '/encrypted/';
    $pages = [];
    $output = '';

    // Loop through the directory using DirectoryIterator
    // Skip '.' and '..' entries, and only consider files with '.data' extension
    foreach (new DirectoryIterator($dirPath) as $fileInfo) {
        if ($fileInfo->isDot()) continue;
        if ($fileInfo->getExtension() === 'data') {
            $pages[] = $fileInfo->getBasename('.data');
        }
    }

    // Sort the page names alphabetically
    sort($pages);

    // Loop through the sorted pages and read their contents
    // Generate HTML list items using the listItem function
    foreach ($pages as $page) {
        $path = $dirPath . $page . '.data';
        $contents = file_get_contents($path);
        if ($contents !== false) {
            $output .= listItem(false, $page, $contents);
        }
    }

    // Add a new, empty list item at the end
    $output .= listItem(true);

    return $output;
}
