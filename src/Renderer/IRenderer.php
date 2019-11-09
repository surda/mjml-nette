<?php declare(strict_types=1);

namespace Surda\Mjml\Renderer;

interface IRenderer
{
    /**
     * @param string $file
     * @return string
     */
    public function render(string $file): string;
}