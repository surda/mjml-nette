# [MJML](https://github.com/mjmlio/mjml) integration into Nette Framework.

[![Build Status](https://travis-ci.org/surda/mjml-nette.svg?branch=master)](https://travis-ci.org/surda/mjml-nette)
[![Licence](https://img.shields.io/packagist/l/surda/mjml-nette.svg?style=flat-square)](https://packagist.org/packages/surda/mjml-nette)
[![Latest stable](https://img.shields.io/packagist/v/surda/mjml-nette.svg?style=flat-square)](https://packagist.org/packages/surda/mjml-nette)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

## Installation

The recommended way to is via Composer:

```
composer require surda/mjml-nette
```

After that you have to register extensions in config.neon:

### Binary renderer

```yaml
extensions:
    mjml: Surda\Mjml\DI\MjmlExtension
    mjml.renderer: Surda\Mjml\DI\MjmlBinaryRendererExtension
```

List of all configuration options:

```yaml
mjml:
    debug: %debugMode%
    tempDir: %tempDir%/cache/latte
    templateFactory: \Surda\Mjml\TemplateFactory

mjml.renderer:
    renderer: \Surda\Mjml\Renderer\BinaryRenderer
    options:
        bin: mjml
        minify: FALSE
        validationLevel: strict
        beautify: TRUE
```
Install [MJML](https://mjml.io)

```bash
$ npm install -g mjml
```

### API renderer

```yaml
extensions:
    mjml: Surda\Mjml\DI\MjmlExtension
    mjml.renderer: Surda\Mjml\DI\MjmlApiRendererExtension
```
Minimal configuration:
```yaml
mjml.renderer:
    options:
        applicationId: 'application-id'
        secretKey: 'secret-key'
```
List of all configuration options:

```yaml
mjml:
    debug: %debugMode%
    tempDir: %tempDir%/cache/latte
    templateFactory: \Surda\Mjml\TemplateFactory

mjml.renderer:
    renderer: \Surda\Mjml\Renderer\ApiRenderer
    options:
        applicationId: 'application-id'
        secretKey: 'secret-key'
        uri: 'https://api.mjml.io/v1/render'
```

## Usage

Template <code>template.mjml</code>

```html
<mjml>
    <mj-body>
        <mj-section>
            <mj-column>
                <mj-image width="100px" src="https://mjml.io/assets/img/logo-small.png"></mj-image>
                <mj-divider border-color="#F45E43"></mj-divider>
                <mj-text font-size="20px" color="#F45E43" font-family="helvetica">Hello {$foo}</mj-text>
            </mj-column>
        </mj-section>
    </mj-body>
</mjml>
```

```php
use Surda\Mjml\MjmlTemplateFactory;

class MailSender 
{
    /** @var MjmlTemplateFactory */
    private $mjmlTemplateFactory;
    
    /**
     * @param MjmlTemplateFactory $mjmlTemplateFactory
     */
    public function __construct(MjmlTemplateFactory $mjmlTemplateFactory)
    {
        $this->mjmlTemplateFactory = $mjmlTemplateFactory;
    }
    
    public function sendEmail(): void
    {
        $template = $this->mjmlTemplateFactory->create();
        $template->setFile('/path/to/template.mjml');
        $template->setParameters(['foo' => 'World']);

        $mail = new Message;
        $mail->setHtmlBody($template);
        
        // or

        $template = $this->mjmlTemplateFactory->create();

        $mail = new Message;
        $mail->setHtmlBody($template->renderToString('/path/to/template.mjml', ['foo' => 'World'])));

        // ...
    }
}
```

Mail

![mail](https://raw.githubusercontent.com/surda/mjml-nette/master/doc/mail.png)

## Others

Only render *.latte template from *.mjml template 

```php
use Surda\Mjml\Engine;

class Convertor 
{
    /** @var Engine */
    private $engine;
    
    /**
     * @param Engine $engine
     */
    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }
    
    public function convert(): void
    {
        $mjmlFile = '/path/to/template.mjml';
        $latteFile = $this->engine->renderLatteFile($mjmlFile);

        // or

        $mjmlFile = '/path/to/template.mjml';
        $latteFile = '/path/to/template.latte';
        $this->engine->renderLatteFile($mjmlFile, $latteFile);
    }
}
```

---

More in the [MJML documentation](https://mjml.io/documentation/).
