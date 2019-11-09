<?php declare(strict_types=1);

namespace Surda\Mjml;

use Nette\Application\LinkGenerator;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;

class TemplateFactory implements ITemplateFactory
{
    /** @var ITemplateFactory */
    private $templateFactory;

    /** @var LinkGenerator */
    private $linkGenerator;

    /**
     * @param ITemplateFactory $templateFactory
     * @param LinkGenerator    $linkGenerator
     */
    public function __construct(ITemplateFactory $templateFactory, LinkGenerator $linkGenerator)
    {
        $this->templateFactory = $templateFactory;
        $this->linkGenerator = $linkGenerator;
    }

    function createTemplate(Control $control = NULL): ITemplate
    {
        /** @var Template $template */
        $template = $this->templateFactory->createTemplate();

        // For macros {link} {plink}
        $template->getLatte()->addProvider('uiControl', $this->linkGenerator);

        return $template;
    }
}