<?php declare(strict_types=1);

namespace Surda\Mjml;

use Surda\Mjml\Renderer\IRenderer;

class Engine
{
    /** @var IRenderer */
    private $renderer;

    /** @var string */
    private $tempDirectory;

    /** @var bool */
    private $autoRefresh = TRUE;

    /**
     * @param string    $tempDirectory
     * @param bool      $autoRefresh
     * @param IRenderer $renderer
     */
    public function __construct(string $tempDirectory, bool $autoRefresh, IRenderer $renderer)
    {
        $this->tempDirectory = $tempDirectory;
        $this->autoRefresh = $autoRefresh;
        $this->renderer = $renderer;
    }

    /**
     * @param string      $mjmlFile
     * @param string|null $latteFile
     * @return string latte file
     */
    public function renderLatteFile(string $mjmlFile, ?string $latteFile = NULL): string
    {
        if (!is_file($mjmlFile)) {
            throw new \RuntimeException("Missing MJML template file '$mjmlFile'.");
        }

        $latteFile = $latteFile ?: $this->getCacheFile($mjmlFile);

        if (!$this->isExpired($mjmlFile, $latteFile) && file_exists($latteFile)) { // @ - latteFile may not exist
            return $latteFile;
        }

        if (!is_dir($this->tempDirectory) && !@mkdir($this->tempDirectory) && !is_dir($this->tempDirectory)) { // @ - dir may already exist
            throw new \RuntimeException("Unable to create directory '$this->tempDirectory'. " . error_get_last()['message']);
        }

        $handle = @fopen("$latteFile.lock", 'c+'); // @ is escalated to exception
        if ($handle === FALSE) {
            throw new \RuntimeException("Unable to create latteFile '$latteFile.lock'. " . error_get_last()['message']);
        } elseif (!@flock($handle, LOCK_EX)) { // @ is escalated to exception
            throw new \RuntimeException("Unable to acquire exclusive lock on '$latteFile.lock'. " . error_get_last()['message']);
        }

        if (!is_file($latteFile) || $this->isExpired($mjmlFile, $latteFile)) {
            $content = $this->renderer->render($mjmlFile);
            if (file_put_contents("$latteFile.tmp", $content) !== strlen($content) || !rename("$latteFile.tmp", $latteFile)) {
                @unlink("$latteFile.tmp"); // @ - latteFile may not exist
                throw new \RuntimeException("Unable to create '$latteFile'.");
            } elseif (function_exists('opcache_invalidate')) {
                @opcache_invalidate($latteFile, TRUE); // @ can be restricted
            }
        }

        if (file_exists($latteFile) === FALSE) {
            throw new \RuntimeException("Unable to load '$latteFile'.");
        }

        flock($handle, LOCK_UN);
        fclose($handle);
        @unlink("$latteFile.lock"); // @ latteFile may become locked on Windows

        return $latteFile;
    }

    /**
     * @param string $file
     * @return string
     */
    private function getCacheFile(string $file): string
    {
        $hash = substr(md5($file), 0, 10);
        $base = boolval(preg_match('#([/\\\\][\w@.-]{3,35}){1,3}$#D', $file, $m))
            ? preg_replace('#[^\w@.-]+#', '-', substr($m[0], 1)) . '--'
            : '';

        return "$this->tempDirectory/$base$hash.latte";
    }

    /**
     * @param string $mjmlFile
     * @param string $latteFile
     * @return bool
     */
    private function isExpired(string $mjmlFile, string $latteFile): bool
    {
        if ($this->autoRefresh === FALSE) {
            return FALSE;
        }

        $mjmlMtime = @filemtime($mjmlFile); // @ - stat may fail
        $latteMtime = @filemtime($latteFile); // @ - stat may fail

        return $mjmlMtime === FALSE || $mjmlMtime > $latteMtime;
    }
}