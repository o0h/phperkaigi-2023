<?php

namespace O0h\KantanFw\Test\Case\Http;

use O0h\KantanFw\Http\Emitter;
use O0h\KantanFw\Http\Message\Response;
use O0h\KantanFw\Http\Message\StreamFactory;
use O0h\KantanFw\Test\Asset\HeaderStack;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

#[CoversClass(Emitter::class)]
class EmitterTest extends TestCase
{
    public function testEmit()
    {
        $input = (new Response())
            ->withHeader('X-TEST-FIELD', 'hoge')
            ->withHeader('X-TEST-FIELD-2', 'fuga')
            ->withStatus(201, 'Created')
            ->withBody(
                (new StreamFactory())->createStream('hello world!')
            );
        $subject = new Emitter();

        ob_start();
        $subject->emit($input);
        $actualBody = ob_get_clean();

        $this->assertSame('hello world!', $actualBody);

        $expectedHeader = [
            'HTTP/1.1 201 Created',
            'X-TEST-FIELD: hoge',
            'X-TEST-FIELD-2: fuga',

        ];
        $actualHeader = HeaderStack::list();
        $this->assertSame($expectedHeader, $actualHeader);
    }
}
