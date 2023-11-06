<?php

declare(strict_types=1);

namespace Utils;

use Interfaces\FilesInterface;
use Interfaces\LoadJsonInterface;

/**
 * Class LoadJson
 *
 * Utility class for JSON file output.
 */
class LoadJson implements LoadJsonInterface
{
    private FilesInterface $files;

    /**
     * Constructor.
     *
     * @param FilesInterface $files
     */
    public function __construct(FilesInterface $files)
    {
        $this->files = $files;
    }

    /**
     * @todo
     *
     * @return array The data.
     * @throws \Exception If the docs can't be retrieved.
     */
    public function getData(): array
    {
        $data = [];
        foreach ($this->files->retrieveDocs() as $doc) {
            try {
                $contents = $this->files->retrieveDocContent($doc);
                $data[] = $contents;
            } catch (\Exception $e) {
                throw new \Exception('Doc retrieval failed!');
            }
        }

        return $data;
    }
}
