<?php

declare(strict_types=1);

namespace View;

use Interfaces\EscaperInterface;
use Interfaces\FilesInterface;
use Config\Config;

/**
 * Class View
 *
 * Utility class for View.
 */
class View
{
    private EscaperInterface $escaper;
    private FilesInterface $files;

    /**
     * Constructor.
     *
     * @param EscaperInterface $escaper An instance of the escaper interface.
     * @param FilesInterface $files An instance of the files interface.
     */
    public function __construct(EscaperInterface $escaper, FilesInterface $files)
    {
        $this->escaper = $escaper;
        $this->files = $files;
    }

    /**
     * Displays the main HTML template.
     *
     * Retrieves the main HTML template using a configured path, replaces a specific placeholder with a document list,
     * and returns the final HTML as a string.
     *
     * @return string The HTML output from the main template parsing.
     */
    public function displayMainTemplate(): string
    {
        $mainTemplate = $this->files->retrieveTemplate(Config::MAIN_TEMPLATE_PATH);
        $templateTag = '{{list}}';

        return str_replace($templateTag, $this->displayDocList(), $mainTemplate);
    }

    /**
     * Generates an HTML list of document files.
     *
     * Iterates through an array of documents and generates the HTML output for each.
     * Returns the concatenated HTML as a string.
     *
     * @return string The HTML output containing list items for each encrypted file.
     */
    private function displayDocList(): string
    {
        $output = '';

        foreach ($this->files->retrieveDocs() as $doc) {
            try {
                $contents = $this->files->retrieveDocContent($doc);
                $encryptedContent = $contents['encryptedContent'];
                $output .= $this->displayDoc(false, $doc, $encryptedContent);
            } catch (\Exception $e) {
                $output .= $this->displayError($e->getMessage());
            }
        }

        $output .= $this->displayDoc(true);

        return $output;
    }

    /**
     * Generates HTML for a document based on it's encrypted content.
     *
     * @param bool $new Indicates if this is a new item. If true, additional CSS classes are added.
     * @param string $doc The title of the page.
     * @param string $encryptedContent The encrypted content from the file.
     * @return string The generated HTML list item.
     */
    private function displayDoc(bool $new = false, string $doc = '', string $encryptedContent = ''): string
    {
        $templateTags = [
            '{{classes}}' => $new ? ' class="decrypted new"' : '',
            '{{doc}}' => $this->escaper->escHtml($doc),
            '{{encryptedContent}}' => $this->escaper->escHtml($encryptedContent),
        ];

        $itemTemplate = $this->files->retrieveTemplate(Config::ITEM_TEMPLATE_PATH);
        foreach ($templateTags as $templateTag => $replacementContent) {
            $itemTemplate = str_replace( $templateTag, $replacementContent, $itemTemplate );
        }

        return $itemTemplate;
    }

    /**
     * Generates HTML for an error on a document.
     *
     * @param string $error The error message.
     * @return string The generated HTML list item.
     */
    private function displayError(string $error): string
    {
        //@todo should use a template file.
        $html = '<li>';
        $html .= $this->escaper->escHtml($error);
        $html .= '</li>';

        return $html;
    }

}
