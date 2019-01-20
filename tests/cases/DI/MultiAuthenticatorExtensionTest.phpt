<?php declare(strict_types=1);

namespace Tests\Surda\MultiAuthenticator\DI;

use Surda\MultiAuthenticator\MultiAuthenticator;
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;
use Tester\Assert;
use Tests\Surda\MultiAuthenticator\Authenticator\DebugAuthenticator;
use Tests\Surda\MultiAuthenticator\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class MultiAuthenticatorExtensionTest extends TestCase
{
    public function testExtension()
    {
        $container = $this->createContainer([
            'multiAuthenticator' => [
                'authenticators' => [
                    DebugAuthenticator::class,
                ]
            ]
        ]);

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