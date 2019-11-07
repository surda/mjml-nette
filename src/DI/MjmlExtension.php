<?php declare(strict_types=1);

namespace Surda\Mjml\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Surda\Mjml\Renderer\IRenderer;
use Surda\Mjml\Renderer\BinaryRenderer;
use Surda\Mjml\Engine;
use Surda\Mjml\MjmlTemplateFactory;

/**
 * @property-read stdClass $config
 */
class MjmlExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        $builder = $this->getContainerBuilder();

        return Expect::structure([
            'debug' => Expect::bool($builder->parameters['debugMode']),
            'tempDir' => Expect::string($builder->parameters['tempDir'] . '/cache/latte'),
            'renderer' => Expect::string('binary'),
            'options' => Expect::structure([
                'bin' => Expect::string('mjml'),
                'minify' => Expect::bool(TRUE),
                'validationLevel' => Expect::anyOf('strict', 'soft', 'skip')->default('soft'),
            ]),
        ]);
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->config;

        $renderer = $builder->addDefinition($this->prefix('renderer'))
            ->setType(IRenderer::class)
            ->setFactory(BinaryRenderer::class, [$config->options->bin, $config->options->minify, $config->options->validationLevel]);

        $builder->addDefinition($this->prefix('engine'))
            ->setFactory(Engine::class, [$config->tempDir, $config->debug, $renderer]);

        $builder->addDefinition($this->prefix('factory'))
            ->setFactory(MjmlTemplateFactory::class);
    }
}