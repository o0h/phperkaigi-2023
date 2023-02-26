<?php

class View
{
    protected string $baseDir;
    protected array $defaults;
    protected array $layoutVariables = [];

    public function __construct($baseDir, $defaults = [])
    {
        $this->baseDir = $baseDir;
        $this->defaults = $defaults;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setLayoutVar($name, $value)
    {
        $this->layoutVariables[$name] = $value;
    }

    /**
     * @param $_path
     * @param $_variables
     * @param $_layout
     * @return false|string
     */
    public function render($_path, $_variables = [], $_layout = false)
    {
        $_file = $this->baseDir . '/' . $_path . '.php';

        extract(array_merge($this->defaults, $_variables));

        ob_start();
        ob_implicit_flush(0);

        require $_file;

        $content = ob_get_clean();

        if ($_layout) {
            $content = $this->render($_layout, [
                ...$this->layoutVariables, ...['_content' => $content]
            ]);

        }

        return $content;
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

}