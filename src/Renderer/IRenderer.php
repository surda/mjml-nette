<?php declare(strict_types=1);

namespace Surda\Mjml\Renderer;

interface IRenderer
{
    /**
     * @param string $content
     * @return string
     */
    public function render(string $content): string;
}