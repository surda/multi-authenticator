<?php declare(strict_types=1);

namespace Tests\Surda\MultiAuthenticator\DI;

use Nette\Security\IIdentity;
use Surda\MultiAuthenticator\MultiAuthenticator;
use Tester\Assert;
use Nette\DI\Statement;
use Tests\Surda\MultiAuthenticator\Authenticator\DebugAuthenticator;
use Tests\Surda\MultiAuthenticator\TestCase;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class MultiAuthenticatorTest extends TestCase
{
    public function testAuthenticator()
    {
        $config = [
            'multiAuthenticator' => [
                'default' => new Statement(DebugAuthenticator::class, [TRUE, 1, 'default']),
                'authenticators' => [
                    new Statement(DebugAuthenticator::class, [TRUE, 2, 'ldap', ['ad.domain.com\\', '@@ad.domain.com']]),
                ]
            ]
        ];

        $container = $this->createContainer($config);

        /** @var MultiAuthenticator $authenticator */
        $authenticator = $container->getByType(MultiAuthenticator::class);

        Assert::type(IIdentity::class, $authenticator->authenticate(['ad.domain.com\username', 'password']));

        Assert::same(1, $authenticator->authenticate(['username', 'password'])->getId());
        Assert::same(2, $authenticator->authenticate(['ad.domain.com\username', 'password'])->getId());
    }
}

(new MultiAuthenticatorTest())->run();