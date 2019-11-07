<?php declare(strict_types=1);

namespace Surda\Mjml\Renderer;

use Symfony\Component\Process\Process;

class BinaryRenderer implements IRenderer
{
    /** @var string */
    private $bin;

    /** @var bool */
    private $minify;

    /** @var string */
    private $validationLevel;

    /**
     * @param string $bin
     * @param bool   $minify
     * @param string $validationLevel
     */
    public function __construct(string $bin, bool $minify, string $validationLevel)
    {
        $this->bin = $bin;
        $this->minify = $minify;
        $this->validationLevel = $validationLevel;
    }

    /**
     * @param string $content
     * @return string
     */
    public function render(string $content): string
    {
        $arguments = [
            $this->bin,
            '-i',
            '-s',
        ];

        array_push($arguments, '--config.validationLevel', $this->validationLevel);

        if ($this->minify === TRUE) {
            array_push($arguments, '--config.minify', 'true');
        }

        $process = new Process($arguments);
        $process->setInput($content);
        $process->mustRun();

        return $process->getOutput();
    }
}