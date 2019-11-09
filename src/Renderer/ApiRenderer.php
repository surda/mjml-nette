<?php declare(strict_types=1);

namespace Surda\Mjml\Renderer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Http\Message\ResponseInterface;

class ApiRenderer implements IRenderer
{
    /** @var string */
    private $uri;

    /** @var string */
    private $applicationId;

    /** @var string */
    private $secretKey;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->uri = $options['uri'];
        $this->applicationId = $options['applicationId'];
        $this->secretKey = $options['secretKey'];
    }

    /**
     * @param string $file
     * @return string
     * @throws \RuntimeException
     */
    public function render(string $file): string
    {
        if (FALSE === $content = file_get_contents($file)) {
            throw new \RuntimeException("Unable to load '$file'.");
        }

        $response = $this->request($content);

        try {
            $json = Json::decode($response);
        }
        catch (JsonException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return $json->html;
    }

    /**
     * @param string $content
     * @return string
     * @throws \RuntimeException
     */
    private function request(string $content): string
    {
        $client = new Client();

        try {
            /** @var ResponseInterface $response */
            $response = $client->request(
                'POST',
                $this->uri,
                [
                    'auth' => [
                        $this->applicationId,
                        $this->secretKey,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Accepts' => 'application/json',
                    ],
                    'json' => [
                        'mjml' => $content,
                    ],
                ]
            );
        }
        catch (GuzzleException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return (string) $response->getBody();
    }
}