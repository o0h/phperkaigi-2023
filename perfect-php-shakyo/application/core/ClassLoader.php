<?php

/**
 * クラスのオートローディングを実現する
 */
class ClassLoader
{
    protected $dirs;

    /**
     * オートローディングのロジックの登録を行う
     *
     * @link https://www.php.net/manual/ja/function.spl-autoload-register.php
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * オートロードの対象とするディレクトリを登録する
     *
     * @param string $dir
     * @return void
     */
    public function registerDir($dir)
    {
        $this->dirs[] = $dir;
    }

    /**
     * クラスのロードを行う
     *
     * @param class-string $class
     * @return void
     */
    public function loadClass($class)
    {
        foreach ($this->dirs as $dir) {
            $file = $dir . '/' . $class . '.php';
            if (is_readable($file)) {
                require $file;

                return;
            }
        }
    }
}