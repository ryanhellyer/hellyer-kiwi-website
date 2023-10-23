<?php

declare(strict_types=1);

namespace Interfaces;

/**
 * Escaper Interface
 *
 * Provides methods for safely escaping attributes and HTML content.
 */
interface EscaperInterface
{
    /**
     * Escapes HTML attributes.
     *
     * @param string $attr The attribute to be escaped.
     * @return string The escaped attribute.
     */
    public function escAttr(string $attr): string;

    /**
     * Escapes HTML content.
     *
     * @param string $html The HTML content to be escaped.
     * @return string The escaped HTML content.
     */
    public function escHtml(string $html): string;
}
