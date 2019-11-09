<?php declare(strict_types=1);

namespace Surda\Mjml\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Surda\Mjml\Renderer\IRenderer;
use Surda\Mjml\Renderer\BinaryRenderer;

/**
 * @property-read stdClass $config
 */
class MjmlBinaryRendererExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'renderer' => Expect::anyOf(
                Expect::string(),
                Expect::type(Statement::class)
            )->default(BinaryRenderer::class),
            'options' => Expect::structure([
                'bin' => Expect::string('mjml'),
                'minify' => Expect::bool(FALSE),
                'validationLevel' => Expect::anyOf('strict', 'soft', 'skip')->default('soft'),
                'beautify' => Expect::bool(TRUE),
            ]),
        ]);
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->config;

        $builder->addDefinition($this->prefix('binary'))
            ->setType(IRenderer::class)
            ->setFactory($config->renderer, [(array) $config->options]);
    }
}