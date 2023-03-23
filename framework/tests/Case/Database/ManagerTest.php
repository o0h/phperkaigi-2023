<?php

namespace O0h\KantanFw\Test\Case\Database;

use O0h\KantanFw\Database\Manager;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ManagerTest extends TestCase
{
    private Manager $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Manager();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConnect(): Manager
    {
        $this->subject->connect('some_db', [
            'dsn' => 'sqlite::memory:',
            'user' => 'user',
            'password' => 'password',
            'options' => ['options'],
        ]);

        return $this->subject;
    }

    /**
     * @depends testConnect
     */
    public function testGetConnection(Manager $subject): void
    {
        $actual = $subject->getConnection('some_db');
        $this->assertInstanceOf(PDO::class, $actual);
    }

    /**
     * @depends testConnect
     */
    public function testGetConnection_未設定の時はnullを返す(Manager $subject): void
    {
        $this->expectException(RuntimeException::class);
        $subject->getConnection('some_db2');
    }

}
