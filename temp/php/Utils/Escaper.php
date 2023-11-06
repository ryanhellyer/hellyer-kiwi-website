<?php

declare(strict_types=1);

namespace Utils;

use Interfaces\EscaperInterface;

/**
 * Class Escaper
 *
 * Utility class for HTML escaping.
 */
class Escaper implements EscaperInterface
{
    /**
     * Escapes HTML attributes to prevent XSS and other attacks.
     *
     * @param string $attr The attribute to be escaped.
     * @return string The escaped attribute.
     */
    public function escAttr(string $attr): string
    {
        return htmlspecialchars($attr, ENT_QUOTES);
    }

    /**
     * Escapes HTML content to prevent XSS attacks.
     *
     * @param string $html The HTML content to be escaped.
     * @return string The escaped HTML content.
     */
    public function escHtml(string $html): string
    {
        return htmlspecialchars($html);
    }
}
