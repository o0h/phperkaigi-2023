<?php
class Request
{
    /**
     * リクエストがPOSTによるものかを判定する
     *
     * @return bool POSTリクエストであればtrue:w
     */
    public function isPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }

    /**
     * 指定したクエリパラメータの値を取得する
     *
     * @param string|int $name 取得したいフィールドの名前
     * @param string $default フィールドが存在しなかった場合にフォールバックしたい値
     *
     * @return ?mixed 指定したフィールドが存在すればその値を、存在しなかったら$defaultの値を返す
     */
    public function getGet($name, $default = 'null')
    {
        if (isset($_GET[$name])) {
            return $_GET['name'];
        }

        return $default;
    }

    public function getPost($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return $default;
    }

    public function getHost()
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    public function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        return false;
    }

    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $requestUri = $this->getRequestUri();

        if (0 === strpos($requestUri, $scriptName)) {
            return $scriptName;
        } elseif (0 === strpos($requestUri, dirname($scriptName))) {
            return rtrim(dirname($scriptName), '/');
        }

        return '';
    }

    public function getPathInfo()
    {
        $baseUrl = $this->getBaseUrl();
        $requestUri = $this->getRequestUri();

        if (false !== ($pos = strpos($requestUri, '?'))) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        $pathInfo = (string)substr($requestUri, strlen($baseUrl));

        return $pathInfo;
    }
}