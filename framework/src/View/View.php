<?php

declare(strict_types=1);

namespace O0h\KantanFw\View;

use O0h\KantanFw\Http\Exception\MissingTemplateException;

class View
{
    protected array $layoutVariables = [];

    public function __construct(protected string $baseDir, protected array $defaults = [])
    {
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setLayoutVar(string $name, mixed $value): void
    {
        $this->layoutVariables[$name] = $value;
    }

    public function render(string $_path, array $_variables = [], string|false $_layout = false): false|string
    {
        $_file = $this->baseDir . '/' . $_path . '.php';
        if (!file_exists($_file)) {
            $errorMessage = sprintf('Template file "%s" is missing.', $_file);
            throw new MissingTemplateException($errorMessage);
        }

        extract(array_merge($this->defaults, $_variables));

        ob_start();
        ob_implicit_flush(false);

        require $_file;

        $content = ob_get_clean();

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
