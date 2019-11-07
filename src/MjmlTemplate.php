<?php declare(strict_types=1);

namespace Surda\Mjml;

use Nette\Application\UI\ITemplate;

class MjmlTemplate
{
    /** @var string */
    private $file;

    /** @var string */
    private $latteFile;

    /** @var array */
    private $params = [];

    /** @var ITemplate */
    private $template;

    /** @var Engine */
    private $engine;

    /**
     * @param ITemplate $template
     * @param Engine    $engine
     */
    public function __construct(ITemplate $template, Engine $engine)
    {
        $this->template = $template;
        $this->engine = $engine;
    }

    /**
     * @param string|null $file MJML file
     * @param string|null $latteFile Latte file
     * @return string
     */
    public function createTemplate(?string $file = NULL, ?string $latteFile = NULL): string
    {
        return $this->engine->createTemplate($file ?: $this->file, $latteFile ?: $this->latteFile);
    }

    /**
     * @param string|null $file MJML file
     * @param array       $params
     */
    public function render(?string $file = NULL, array $params = []): void
    {
        $latteFile = $this->createTemplate($file ?: $this->file);

        $this->template->setFile($latteFile);
        $this->template->setParameters($params + $this->params);
        $this->template->render();
    }

    /**
     * @param string|null $file MJML file
     * @param array       $params
     * @return string
     */
    public function renderToString(?string $file = NULL, array $params = []): string
    {
        $latteFile = $this->createTemplate($file ?: $this->file);

        $this->template->setFile($latteFile);
        $this->template->setParameters($params + $this->params);

        return $this->template->renderToString();
    }

    /**
     * Renders template to string.
     *
     * @return string
     * @throws \Throwable
     */
    public function __toString(): string
    {
        try {
            return $this->renderToString();
        }
        catch (\Throwable $e) {
            throw $e;
        }
    }

    /* ******************************************************************************************** */

    /**
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return static
     */
    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLatteFile(): ?string
    {
        return $this->latteFile;
    }

    /**
     * @param string $latteFile
     * @return static
     */
    public function setLatteFile(string $latteFile): self
    {
        $this->latteFile = $latteFile;

        return $this;
    }


    /**
     * @param array $params
     * @return static
     */
    public function setParameters(array $params): self
    {
        $this->params = $params + $this->params;

        return $this;
    }


    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->params;
    }

    /**
     * @return ITemplate
     */
    public function getTemplate(): ITemplate
    {
        return $this->template;
    }

    /**
     * @return Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }
}