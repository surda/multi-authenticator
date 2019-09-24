<?php declare(strict_types=1);

namespace Tests\Surda\MultiAuthenticator\DI;

use Nette\DI\Container;
use Nette\DI\Statement;
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;
use Tests\Surda\MultiAuthenticator\Authenticator\DebugAuthenticator;
use Tester\Assert;
use Tester\TestCase;
use Tests\Surda\MultiAuthenticator\ContainerFactory;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class AuthenticatorResolverTest extends TestCase
{
    public function testDefaultAuthenticator()
    {
        /** @var Container $container */
        $container = (new ContainerFactory())->create([
            'multiAuthenticator' => [
                'authenticators' => [
                    'default' => new Statement(DebugAuthenticator::class, [TRUE, 'default']),
                ],
            ],
        ], 3);

        Assert::same('default', $container->getByType(AuthenticatorResolver::class)->getDefaultAuthenticator()->type);

        /** @var Container $container */
        $container2 = (new ContainerFactory())->create([
            'multiAuthenticator' => [
                'default' => new Statement(DebugAuthenticator::class, [TRUE, 'default']),
            ],
        ], 4);

        Assert::same('default', $container->getByType(AuthenticatorResolver::class)->getDefaultAuthenticator()->type);

        /** @var Container $container */
        $container3 = (new ContainerFactory())->create([
            'multiAuthenticator' => [
                'default' => new Statement(DebugAuthenticator::class, [TRUE, 'default']),
                'authenticators' => [
                    'default' => new Statement(DebugAuthenticator::class, [TRUE, 'default2']),
                ],
            ],
        ], 5);

        Assert::same('default', $container->getByType(AuthenticatorResolver::class)->getDefaultAuthenticator()->type);
    }

    public function testAuthenticatorByUsername()
    {
        /** @var Container $container */
        $container = (new ContainerFactory())->create([
            'multiAuthenticator' => [
                'rules' => [
                    'ldap' => [
                        '#^.+@ad.domain.com$#',
                    ],
                    'db' => [
                        '#^.+bar$#',
                    ],
                ],
                'authenticators' => [
                    'default' => new Statement(DebugAuthenticator::class, [TRUE, 'default']),
                    'ldap' => new Statement(DebugAuthenticator::class, [TRUE, 'ldap']),
                ],
            ],
        ], 1);

        /** @var AuthenticatorResolver $resolver */
        $resolver = $container->getByType(AuthenticatorResolver::class);

        Assert::same('ldap', $resolver->resolveByUsername('username@ad.domain.com')->type);
        Assert::same('default', $resolver->resolveByUsername('username@ad.domain.org')->type);

        Assert::exception(function () use ($resolver) {
            $resolver->resolveByUsername('foobar');
        }, \Surda\MultiAuthenticator\Exception\AuthenticatorNotFoundException::class, 'Authenticator type \'db\' is not registered.');
    }

    public function testAuthenticatorByType()
    {
        /** @var Container $container */
        $container = (new ContainerFactory())->create([
            'multiAuthenticator' => [
                'authenticators' => [
                    'default' => new Statement(DebugAuthenticator::class, [TRUE, 'default']),
                    'ldap' => new Statement(DebugAuthenticator::class, [TRUE, 'ldap']),
                ],
            ],
        ], 2);

        /** @var AuthenticatorResolver $resolver */
        $resolver = $container->getByType(AuthenticatorResolver::class);

        Assert::same('ldap', $resolver->resolveByType('ldap')->type);
        Assert::same('default', $resolver->resolveByType('default')->type);

        Assert::exception(function () use ($resolver) {
            $resolver->resolveByType('db');
        }, \Surda\MultiAuthenticator\Exception\AuthenticatorNotFoundException::class, 'Authenticator type \'db\' is not registered.');

        Assert::same('default', $resolver->getDefaultAuthenticator()->type);
    }
}

(new AuthenticatorResolverTest())->run();