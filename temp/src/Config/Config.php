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
    //@todo next two need renamed.
    public const DIR_PATH = '/var/www/pressabl/public_html/temp/encrypted/';
    public const TEMP_DIR_PATH = '/var/www/pressabl/public_html/temp/';

    public const MAIN_TEMPLATE_PATH = self::TEMP_DIR_PATH . 'templates/main-template.tmpl';
    public const ITEM_TEMPLATE_PATH = self::TEMP_DIR_PATH . 'templates/item-template.tmpl';

    public const SEPARATOR = '<<<SEPARATOR>>>';

    const STORAGE_FILE_EXTENSION = 'data';
}
