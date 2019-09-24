<?php declare(strict_types=1);

namespace Surda\MultiAuthenticator\Resolver;

use Nette\Security\IAuthenticator;
use Nette\Utils\Strings;
use Surda\MultiAuthenticator\Exception\AuthenticatorNotFoundException;

class AuthenticatorResolver
{
    /** @var IAuthenticator[] */
    private $authenticators = [];

    /** @var IAuthenticator */
    private $defaultAuthenticator;

    /** @var array */
    private $rules;

    /**
     * @param array $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * @param string $username
     * @return IAuthenticator
     * @throws AuthenticatorNotFoundException
     */
    public function resolveByUsername(string $username): IAuthenticator
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
     * @param string $type
     * @return IAuthenticator
     * @throws AuthenticatorNotFoundException
     */
    public function resolveByType(string $type): IAuthenticator
    {
        if (array_key_exists($type, $this->authenticators)) {
            return $this->authenticators[$type];
        }

        throw new AuthenticatorNotFoundException(sprintf("Authenticator type '%s' is not registered.", $type));
    }

    /**
     * @param string         $type
     * @param IAuthenticator $authenticator
     */
    public function addAuthenticator(string $type, IAuthenticator $authenticator): void
    {
        $this->authenticators[$type] = $authenticator;
    }

    /**
     * @return IAuthenticator
     * @throws AuthenticatorNotFoundException
     */
    public function getDefaultAuthenticator(): IAuthenticator
    {
        if ($this->defaultAuthenticator === NULL) {
            throw new AuthenticatorNotFoundException();
        }

        return $this->defaultAuthenticator;
    }

    /**
     * @param IAuthenticator $authenticator
     */
    public function setDefaultAuthenticator(IAuthenticator $authenticator): void
    {
        $this->defaultAuthenticator = $authenticator;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    /**
     * @return IAuthenticator[]
     */
    public function getAuthenticators(): array
    {
        return $this->authenticators;
    }
}