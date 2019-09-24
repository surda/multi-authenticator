<?php declare(strict_types=1);

namespace Tests\Surda\MultiAuthenticator\DI;

use Nette\DI\Container;
use Surda\MultiAuthenticator\MultiAuthenticator;
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;
use Tester\Assert;
use Tester\TestCase;
use Tests\Surda\MultiAuthenticator\ContainerFactory;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class MultiAuthenticatorExtensionTest extends TestCase
{
    public function testExtension()
    {
        /** @var Container $container */
        $container = (new ContainerFactory())->create([],1);

        $resolver = $container->getService('multiAuthenticator.resolver');
        Assert::true($resolver instanceof AuthenticatorResolver);

        $resolver = $container->getByType(AuthenticatorResolver::class);
        Assert::true($resolver instanceof AuthenticatorResolver);

        $authenticator = $container->getService('multiAuthenticator.authenticator');
        Assert::true($authenticator instanceof MultiAuthenticator);

        $authenticator = $container->getByType(MultiAuthenticator::class);
        Assert::true($authenticator instanceof MultiAuthenticator);
    }
}

(new MultiAuthenticatorExtensionTest())->run();