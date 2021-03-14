<?php declare(strict_types=1);

namespace Surda\MultiAuthenticator\Resolver;

use Nette\Security\Authenticator;
use Nette\Utils\Strings;
use Surda\MultiAuthenticator\Exception\AuthenticatorNotFoundException;

class AuthenticatorResolver
{
    /** @var Authenticator[] */
    private $authenticators = [];

    /** @var Authenticator */
    private $defaultAuthenticator;

    /** @var array<mixed> */
    private $rules;

    /**
     * @param array<mixed> $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * @throws AuthenticatorNotFoundException
     */
    public function resolveByUsername(string $username): Authenticator
    {
        foreach ($this->getRules() as $authenticatorType => $patterns) {
            foreach ($patterns as $pattern) {
                if (Strings::match($username, $pattern) !== NULL) {
                    return $this->resolveByType($authenticatorType);
                }
            }
        }

        if ($this->defaultAuthenticator !== NULL) {
            return $this->getDefaultAuthenticator();
        }

        throw new AuthenticatorNotFoundException();
    }

    /**
     * @throws AuthenticatorNotFoundException
     */
    public function resolveByType(string $type): Authenticator
    {
        if (array_key_exists($type, $this->authenticators)) {
            return $this->authenticators[$type];
        }

        throw new AuthenticatorNotFoundException(sprintf("Authenticator type '%s' is not registered.", $type));
    }

    public function addAuthenticator(string $type, Authenticator $authenticator): void
    {
        $this->authenticators[$type] = $authenticator;
    }

    /**
     * @throws AuthenticatorNotFoundException
     */
    public function getDefaultAuthenticator(): Authenticator
    {
        if ($this->defaultAuthenticator === NULL) {
            throw new AuthenticatorNotFoundException();
        }

        return $this->defaultAuthenticator;
    }

    public function setDefaultAuthenticator(Authenticator $authenticator): void
    {
        $this->defaultAuthenticator = $authenticator;
    }

    /**
     * @return array<mixed>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param array<mixed> $rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    /**
     * @return Authenticator[]
     */
    public function getAuthenticators(): array
    {
        return $this->authenticators;
    }
}