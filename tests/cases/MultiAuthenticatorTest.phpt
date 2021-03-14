<?php declare(strict_types=1);

namespace Tests\Surda\MultiAuthenticator;

use Nette\DI\Container;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Surda\MultiAuthenticator\MultiAuthenticator;
use Tester\Assert;
use Nette\DI\Definitions\Statement;
use Tester\TestCase;
use Tests\Surda\MultiAuthenticator\Authenticator\DebugAuthenticator;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class MultiAuthenticatorTest extends TestCase
{
    public function testAuthentification()
    {
        /** @var Container $container */
        $container = (new ContainerFactory())->create([
            'multiAuthenticator' => [
                'rules' => [
                    'ldap' => [
                        '#^.+@ad.domain.com$#',
                    ],
                ],
                'authenticators' => [
                    'default' => new Statement(DebugAuthenticator::class, [TRUE, 'default', 1]),
                    'ldap' => new Statement(DebugAuthenticator::class, [TRUE, 'ldap', 2]),
                ],
            ],
        ], 10);

        /** @var MultiAuthenticator $authenticator */
        $authenticator = $container->getByType(MultiAuthenticator::class);

        Assert::type(IIdentity::class, $authenticator->authenticate('username', 'password'));

        Assert::same(1, $authenticator->authenticate('username', 'password')->getId());
        Assert::same(2, $authenticator->authenticate('username@ad.domain.com', 'password')->getId());
    }

    public function testFailureAuthentification()
    {
        /** @var Container $container */
        $container = (new ContainerFactory())->create([
            'multiAuthenticator' => [
                'authenticators' => [
                    'default' => new Statement(DebugAuthenticator::class, [FALSE, 'default']),
                ],
            ],
        ], 11);

        /** @var MultiAuthenticator $authenticator */
        $authenticator = $container->getByType(MultiAuthenticator::class);

        Assert::exception(function () use ($authenticator) {
            $authenticator->authenticate('username', 'password');
        }, AuthenticationException::class, 'Cannot login');
    }

    public function testAuthentificatorNotFound()
    {
        /** @var Container $container */
        $container = (new ContainerFactory())->create([], 12);

        /** @var MultiAuthenticator $authenticator */
        $authenticator = $container->getByType(MultiAuthenticator::class);

        Assert::exception(function () use ($authenticator) {
            $authenticator->authenticate('username', 'password');
        }, AuthenticationException::class, 'Authenticator not found.');
    }
}

(new MultiAuthenticatorTest())->run();