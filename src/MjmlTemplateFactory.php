<?php declare(strict_types=1);

namespace Surda\Mjml;

use Nette\Application\UI\ITemplateFactory;

class MjmlTemplateFactory
{
    /** @var ITemplateFactory */
    private $templateFactory;

    /** @var Engine */
    private $engine;

    /**
     * @param ITemplateFactory $templateFactory
     * @param Engine           $engine
     */
    public function __construct(ITemplateFactory $templateFactory, Engine $engine)
    {
        $this->templateFactory = $templateFactory;
        $this->engine = $engine;
    }

    /**
     * @return MjmlTemplate
     */
    public function createTemplate(): MjmlTemplate
    {
        return new MjmlTemplate($this->templateFactory->createTemplate(), $this->engine);
    }
}