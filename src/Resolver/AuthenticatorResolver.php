<?php declare(strict_types=1);

namespace Surda\MultiAuthenticator\Resolver;

use Surda\MultiAuthenticator\Exception\AuthenticatorNotFoundException;
use Surda\MultiAuthenticator\Exception\InvalidAuthenticatorException;
use Surda\MultiAuthenticator\IResolvableAuthenticator;

class AuthenticatorResolver
{
    /** @var IResolvableAuthenticator[] */
    private $authenticators = [];

    /** @var IResolvableAuthenticator */
    private $defaultAuthenticator;

    /**
     * @param string $username
     * @return IResolvableAuthenticator
     * @throws AuthenticatorNotFoundException
     */
    public function resolveByUsername(string $username): IResolvableAuthenticator
    {
        /** @var IResolvableAuthenticator $authenticator */
        foreach ($this->getAuthenticators() as $authenticator) {
            if ($authenticator->isMatched($username)) {
                return $authenticator;
            }
        }

        if ($this->defaultAuthenticator !== NULL) {
            return $this->getDefaultAuthenticator();
        }

        throw new AuthenticatorNotFoundException();
    }

    /**
     * @param string $type
     * @return IResolvableAuthenticator
     * @throws AuthenticatorNotFoundException
     */
    public function resolveByType(string $type): IResolvableAuthenticator
    {
        if (array_key_exists($type, $this->authenticators)) {
            return $this->authenticators[$type];
        }

        throw new AuthenticatorNotFoundException(sprintf("Authenticator type '%s' is not registered.", $type));
    }

    /**
     * @return IResolvableAuthenticator[]
     */
    public function getAuthenticators(): array
    {
        return $this->authenticators;
    }

    /**
     * @return IResolvableAuthenticator
     * @throws AuthenticatorNotFoundException
     */
    public function getDefaultAuthenticator(): IResolvableAuthenticator
    {
        if ($this->defaultAuthenticator === NULL) {
            throw new AuthenticatorNotFoundException();
        }

        return $this->defaultAuthenticator;
    }

    /**
     * @param IResolvableAuthenticator $authenticator
     * @throws InvalidAuthenticatorException
     */
    public function addAuthenticator(IResolvableAuthenticator $authenticator): void
    {
        $this->authenticators[$authenticator->getType()] = $authenticator;
    }

    /**
     * @param IResolvableAuthenticator $authenticator
     * @throws InvalidAuthenticatorException
     */
    public function setDefaultAuthenticator(IResolvableAuthenticator $authenticator): void
    {
        $this->defaultAuthenticator = $authenticator;
    }
}