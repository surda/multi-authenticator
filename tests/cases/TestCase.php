<?php declare(strict_types=1);

namespace Tests\Surda\MultiAuthenticator;

use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Surda\MultiAuthenticator\DI\MultiAuthenticatorExtension;
use Tester;

abstract class TestCase extends Tester\TestCase
{
    /**
     * @param array $config
     * @param mixed  $key
     * @return Container
     */
    protected function createContainer(array $config, $key = NULL): Container
    {
        $loader = new ContainerLoader(TEMP_DIR, TRUE);
        $class = $loader->load(function (Compiler $compiler) use ($config): void {
            $compiler->addConfig($config);
            $compiler->addExtension('multiAuthenticator', new MultiAuthenticatorExtension());
        }, $key);

        return new $class();
    }
}