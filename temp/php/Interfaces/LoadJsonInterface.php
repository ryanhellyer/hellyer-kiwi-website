<?php

declare(strict_types=1);

namespace Interfaces;

/**
 * LoadJson Interface
 *
 * @todo.
 */
interface LoadJsonInterface {

    /**
     * @todo
     *
     * @return array The data.
     * @throws \Exception If the docs can't be retrieved.
     */
    public function getData(): array;
}
