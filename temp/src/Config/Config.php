<?php

declare(strict_types=1);

namespace Config;

/**
 * Class Config
 *
 * Provides configuration data.
 */
class Config
{
    private const DIR_PATH = '/var/www/pressabl/public_html/temp/';

    public const STORAGE_PATH = self::DIR_PATH . 'encrypted/';
    public const MAIN_TEMPLATE_PATH = self::DIR_PATH . 'templates/main-template.tmpl';
    public const ITEM_TEMPLATE_PATH = self::DIR_PATH . 'templates/item-template.tmpl';
    public const SEPARATOR = '<<<SEPARATOR>>>';
    public const STORAGE_FILE_EXTENSION = 'data';
}
