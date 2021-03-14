<?php declare(strict_types=1);

namespace Surda\MultiAuthenticator;

use Nette\Security\Authenticator;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\SmartObject;
use Surda\MultiAuthenticator\Exception\AuthenticatorNotFoundException;
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;

class MultiAuthenticator implements Authenticator
{
    use SmartObject;

    /** @var AuthenticatorResolver */
    private $resolver;

    /**
     * @param AuthenticatorResolver $resolver
     */
    public function __construct(AuthenticatorResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @throws AuthenticationException
     */
    function authenticate(string $user, string $password): IIdentity
    {
        try {
            $authenticator = $this->resolver->resolveByUsername($user);
        }
        catch (AuthenticatorNotFoundException $e) {
            throw new AuthenticationException('Authenticator not found.', self::FAILURE);
        }

        return $authenticator->authenticate($user, $password);
    }
}