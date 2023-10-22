<?php

declare(strict_types=1);

namespace Interfaces;

interface EscaperInterface
{
    public function escAttr(string $attr): string;
    public function escHtml(string $html): string;
}
