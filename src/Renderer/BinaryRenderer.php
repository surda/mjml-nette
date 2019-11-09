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

    /** @var bool */
    private $beautify;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->bin = $options['bin'];
        $this->minify = $options['minify'];
        $this->validationLevel = $options['validationLevel'];
        $this->beautify = $options['beautify'];
    }

    /**
     * @param string $file
     * @return string
     * @throws \RuntimeException
     */
    public function render(string $file): string
    {
        $arguments = [
            $this->bin,
            $file,
            '-s',
        ];

        array_push($arguments, '--config.validationLevel', $this->validationLevel);

        if ($this->beautify === FALSE) {
            array_push($arguments, '--config.beautify', 'false');
        }

        if ($this->minify === TRUE) {
            array_push($arguments, '--config.minify', 'true');
        }

        $process = new Process($arguments);
        $process->setInput($file);
        $process->mustRun();

        return $process->getOutput();
    }
}