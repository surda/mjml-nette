<?php declare(strict_types=1);

namespace Surda\Mjml\DI;

use Nette\Application\UI\ITemplateFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Surda\Mjml\Engine;
use Surda\Mjml\MjmlTemplateFactory;
use Surda\Mjml\TemplateFactory;

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
            'templateFactory' => Expect::anyOf(Expect::string(), Expect::type(Statement::class))->default(TemplateFactory::class),
        ]);
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->config;

        $templateFactory = $builder->addDefinition($this->prefix('templateFactory'))
            ->setType(ITemplateFactory::class)
            ->setFactory($config->templateFactory)
            ->setAutowired(FALSE);

        $engine = $builder->addDefinition($this->prefix('engine'))
            ->setFactory(Engine::class, [$config->tempDir, $config->debug]);

        $builder->addDefinition($this->prefix('factory'))
            ->setFactory(MjmlTemplateFactory::class, [$templateFactory, $engine]);
    }
}