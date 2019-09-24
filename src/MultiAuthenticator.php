<?php declare(strict_types=1);

namespace Surda\MultiAuthenticator;

use Nette\Security\IAuthenticator;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\SmartObject;
use Surda\MultiAuthenticator\Exception\AuthenticatorNotFoundException;
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;

class MultiAuthenticator implements IAuthenticator
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
     * @param array $credentials
     * @return IIdentity
     * @throws AuthenticationException
     */
    function authenticate(array $credentials): IIdentity
    {
        [$username] = $credentials;

        try {
            $authenticator = $this->resolver->resolveByUsername($username);
        }
        catch (AuthenticatorNotFoundException $e) {
            throw new AuthenticationException('Authenticator not found.', self::FAILURE);
        }

        return $authenticator->authenticate($credentials);
    }
}