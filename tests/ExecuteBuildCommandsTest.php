<?php

namespace Ugarit\VaporCli\Tests;

use Heritage\Container\Container;
use Ugarit\VaporCli\BuildProcess\ExecuteBuildCommands;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class ExecuteBuildCommandsTest extends TestCase
{
    protected $testManifest;

    protected function setUp(): void
    {
        parent::setUp();
        touch($this->testManifest = getcwd().'/test.vapor.yml');
        Container::getInstance()->offsetSet('manifest', $this->testManifest);
    }

    protected function tearDown(): void
    {
        @unlink(Container::getInstance()->offsetGet('manifest'));
        parent::tearDown();
    }

    public function test_unsupported_commands_are_removed()
    {
        file_put_contents($this->testManifest, Yaml::dump([
            'environments' => [
                'production' => [
                    'build' => [
                        'pa clear-compiled',
                        'php scribe migrate',
                        'php scribe clear-compiled',
                        'a optimize:clear',
                        'php scribe optimize',
                        'php scribe optimize:clear',
                        'php scribe route:cache',
                    ],
                ],
            ],
        ]));

        $this->assertSame(
            ['php scribe migrate', 'php scribe optimize'],
            (new ExecuteBuildCommands('production'))->supportedCommands()
        );
    }
}
