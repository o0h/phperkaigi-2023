<?php

declare(strict_types=1);

namespace O0h\KantanFw\Http;

use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

// FIXME: ちゃんとテストを書こうね
#[CodeCoverageIgnore]
class Session
{
    protected static bool $sessionStarted = false;
    protected static bool $sessionIdRegenerated = false;

    public function __construct()
    {
        if (!self::$sessionStarted) {
            session_start();

            self::$sessionStarted = true;
        }
    }

    public function set(string $name, mixed $value): void
    {
        $_SESSION[$name] = $value;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return $default;
    }

    public function remove(string $name): void
    {
        unset($_SESSION[$name]);
    }

    public function clear(): void
    {
        $_SESSION = [];
    }

    public function regenerate(bool $destroy = true): void
    {
        if (!self::$sessionStarted) {
            session_regenerate_id($destroy);

            self::$sessionIdRegenerated = true;
        }
    }

    public function setAuthenticated(bool $bool): void
    {
        $this->set('_authenticated', $bool);
    }

    public function isAuthenticated(): bool
    {
        return (bool)$this->get('_authenticated', false);
    }
}
