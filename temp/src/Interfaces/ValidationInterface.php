<?php

declare(strict_types=1);

namespace Interfaces;

interface ValidationInterface
{
    public function checkHash(string $path, string $hash): bool;
    public function validatePostData(array $postData, array $requiredKeys): array|bool;
}
