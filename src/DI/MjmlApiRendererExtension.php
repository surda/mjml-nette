<?php declare(strict_types=1);

namespace Surda\Mjml\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Surda\Mjml\Renderer\ApiRenderer;
use Surda\Mjml\Renderer\IRenderer;

/**
 * @property-read stdClass $config
 */
class MjmlApiRendererExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'renderer' => Expect::anyOf(
                Expect::string(),
                Expect::type(Statement::class)
            )->default(ApiRenderer::class),
            'options' => Expect::structure([
                'applicationId' => Expect::string()->required(),
                'secretKey' => Expect::string()->required(),
                'uri' => Expect::string('https://api.mjml.io/v1/render'),
            ]),
        ]);
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->config;

        $builder->addDefinition($this->prefix('api'))
            ->setType(IRenderer::class)
            ->setFactory($config->renderer, [(array) $config->options]);
    }
}