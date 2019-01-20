<?php declare(strict_types=1);

namespace Surda\MultiAuthenticator;

use Nette\Security\IAuthenticator;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Surda\MultiAuthenticator\Exception\AuthenticatorNotFoundException;
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;

class MultiAuthenticator implements IAuthenticator
{
    /** @var AuthenticatorResolver */
    private $authenticatorResolver;

    /**
     * @param AuthenticatorResolver $authenticatorResolver
     */
    public function __construct(AuthenticatorResolver $authenticatorResolver)
    {
        $this->authenticatorResolver = $authenticatorResolver;
    }

    /**
     * @param array $credentials
     * @return IIdentity
     * @throws AuthenticationException
     */
    function authenticate(array $credentials): IIdentity
    {
        [$username] = $credentials;

        try {
            $authenticator = $this->authenticatorResolver->resolveByUsername($username);
        }
        catch (AuthenticatorNotFoundException $e) {
            throw new AuthenticationException('Authenticator not found.', self::FAILURE);

            // TODO loging
        }

        return $authenticator->authenticate($credentials);
    }
}