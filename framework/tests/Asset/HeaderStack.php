<?php

declare(strict_types=1);

namespace O0h\KantanFw\Test\Asset;

final class HeaderStack
{
    private static array $data = [];
    public static function push(string $header): void
    {
        self::$data[] = $header;
    }

    public static function list(): array
    {
        return self::$data;
    }

    public static function reset(): void
    {
        self::$data = [];
    }
}
