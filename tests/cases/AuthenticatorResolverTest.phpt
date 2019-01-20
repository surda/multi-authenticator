<?php declare(strict_types=1);

namespace Tests\Surda\MultiAuthenticator\DI;

use Nette\DI\Statement;
use Surda\MultiAuthenticator\Exception\AuthenticatorNotFoundException;
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;
use Tester\Assert;
use Tests\Surda\MultiAuthenticator\Authenticator\DebugAuthenticator;
use Tests\Surda\MultiAuthenticator\TestCase;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class AuthenticatorResolverTest extends TestCase
{
    public function testResolveByUsername()
    {
        $config = [
            'multiAuthenticator' => [
                'default' => new Statement(DebugAuthenticator::class, [TRUE, 1, 'default']),
                'authenticators' => [
                    new Statement(DebugAuthenticator::class, [TRUE, 1, 'ldap', ['ad.domain.com\\', '@@ad.domain.com']]),
                ]
            ]
        ];

        $container = $this->createContainer($config, 1);

        /** @var AuthenticatorResolver $resolver */
        $resolver = $container->getByType(AuthenticatorResolver::class);

        Assert::same('ldap', $resolver->resolveByUsername('ad.domain.com\username')->getType());
        Assert::same('ldap', $resolver->resolveByUsername('username@ad.domain.com')->getType());
        Assert::same('default', $resolver->resolveByUsername('username')->getType());
    }

    public function testResolveByType()
    {
        $config = [
            'multiAuthenticator' => [
                'authenticators' => [
                    new Statement(DebugAuthenticator::class, [TRUE, 1, 'foo', ['foo']]),
                    new Statement(DebugAuthenticator::class, [TRUE, 1, 'bar', ['bar']]),
                ]
            ]
        ];
        $container = $this->createContainer($config, 2);

        /** @var AuthenticatorResolver $resolver */
        $resolver = $container->getByType(AuthenticatorResolver::class);

        Assert::same('foo', $resolver->resolveByType('foo')->getType());
        Assert::same('bar', $resolver->resolveByType('bar')->getType());

        Assert::exception(
            function () use ($resolver): void {
                $resolver->resolveByType('test');
            }, AuthenticatorNotFoundException::class, "Authenticator type 'test' is not registered."
        );
    }
}

(new AuthenticatorResolverTest())->run();