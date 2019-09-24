<?php declare(strict_types=1);

namespace Tests\Surda\MultiAuthenticator\Authenticator;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;

class DebugAuthenticator implements IAuthenticator
{
    /** @var bool */
    public $pass;

    /** @var string */
    public $type;

    /** @var IIdentity|null */
    public $identity;

    /** @var int */
    private $id;

    /**
     * @param bool   $pass
     * @param string $type
     * @param int    $id
     */
    public function __construct(bool $pass = TRUE, $type = 'debug', int $id = 1)
    {
        $this->pass = $pass;
        $this->type = $type;
        $this->id = $id;
    }

    public function setIdentity(IIdentity $identity): void
    {
        $this->identity = $identity;
    }

    /**
     * @param array $credentials
     * @return IIdentity
     * @throws AuthenticationException
     */
    function authenticate(array $credentials): IIdentity
    {
        if ($this->pass === FALSE) {
            throw new AuthenticationException('Cannot login', IAuthenticator::FAILURE);
        }

        if ($this->identity !== NULL) {
            return $this->identity;
        }

        return new Identity($this->id, NULL, NULL);
    }
}