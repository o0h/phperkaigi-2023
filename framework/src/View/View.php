<?php

declare(strict_types=1);

namespace O0h\KantanFw\View;

use O0h\KantanFw\Http\Exception\MissingTemplateException;

class View
{
    /**
     * @phpstan-var array<string, mixed> $layoutVariables
     */
    protected array $layoutVariables = [];

    /**
     * @phpstan-param array<string, mixed> $defaults
     */
    public function __construct(protected string $baseDir, protected array $defaults = [])
    {
    }

    public function setLayoutVar(string $name, mixed $value): void
    {
        $this->layoutVariables[$name] = $value;
    }

    /**
     * @param array<string, mixed> $_variables
     */
    public function render(string $_path, array $_variables = [], string|false $_layout = false): string
    {
        $file = $this->baseDir . '/' . $_path . '.php';
        if (!file_exists($file)) {
            $errorMessage = sprintf('Template file "%s" is missing.', $file);
            throw new MissingTemplateException($errorMessage);
        }

        $content = (function ($variables, $_file) {
            extract($variables);

            ob_start();
            ob_implicit_flush(false);

            require $_file;

            $content = (string)ob_get_clean();

            return $content;
        })(array_merge($this->defaults, $_variables), $file);

        if ($_layout) {
            $content = $this->render("layout/{$_layout}", [
                ...$this->layoutVariables, ...['_content' => $content]
            ]);
        }

        return $content;
    }

    public function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
