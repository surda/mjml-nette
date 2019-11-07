<?php declare(strict_types=1);

namespace Tests\Surda\Mjml\Renderer;

use Surda\Mjml\Renderer\BinaryRenderer;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class BinaryRendererTest extends TestCase
{
    public function testBasicRender()
    {
        $renderer = new BinaryRenderer('mjml', FALSE, 'strict');
        $html = $renderer->render(file_get_contents(__DIR__ . '/../fixtures/basic.mjml'));

        Assert::contains('html', $html);
        Assert::contains('Hello World', $html);
    }

    public function testInvalidRender()
    {
        $renderer = new BinaryRenderer('mjml', FALSE, 'strict');

        Assert::exception(function () use ($renderer) {
            $renderer->render(file_get_contents(__DIR__ . '/../fixtures/invalid.mjml'));
        }, \Symfony\Component\Process\Exception\ProcessFailedException::class);
    }

    public function testInvalidRenderWithSkipValidationLevel()
    {
        $renderer = new BinaryRenderer('mjml', FALSE, 'skip');
        $html = $renderer->render(file_get_contents(__DIR__ . '/../fixtures/invalid.mjml'));

        Assert::contains('Hello World', $html);
    }

    public function testInvalidRenderWithSoftValidationLevel()
    {
        $renderer = new BinaryRenderer('mjml', FALSE, 'soft');
        $html = $renderer->render(file_get_contents(__DIR__ . '/../fixtures/invalid.mjml'));

        Assert::contains('Hello World', $html);
    }

    public function testBinaryNotFound()
    {
        $renderer = new BinaryRenderer('mjml-not-found', FALSE, 'soft');

        Assert::exception(function () use ($renderer) {
            $renderer->render(file_get_contents(__DIR__ . '/../fixtures/basic.mjml'));
        }, \Symfony\Component\Process\Exception\ProcessFailedException::class);
    }
}

(new BinaryRendererTest())->run();