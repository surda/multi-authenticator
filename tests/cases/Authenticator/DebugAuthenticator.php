<?php declare(strict_types=1);

namespace Tests\Surda\MultiAuthenticator\Authenticator;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Surda\MultiAuthenticator\IResolvableAuthenticator;

class DebugAuthenticator implements IResolvableAuthenticator
{
    /** @var bool */
    private $pass;

    /** @var int */
    private $id;

    /** @var string */
    private $type;

    /** @var array */
    private $criteria;

    /** @var IIdentity|null */
    private $identity;

    /**
     * @param bool   $pass
     * @param int    $id
     * @param string $type
     * @param array  $criteria
     */
    public function __construct(bool $pass = TRUE, int $id = 1, $type = 'debug', array $criteria = [])
    {
        $this->pass = $pass;
        $this->id = $id;
        $this->type = $type;
        $this->criteria = $criteria;
    }

    public function setIdentity(IIdentity $identity): void
    {
        $this->identity = $identity;
    }

    /**
     * @return IIdentity
     * @throws AuthenticationException
     */
    function authenticate(array $credentials)
    {
        if ($this->pass === FALSE) {
            throw new AuthenticationException('Cannot login', IAuthenticator::FAILURE);
        }

        if ($this->identity !== NULL) {
            return $this->identity;
        }

        return new Identity($this->id, NULL, NULL);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $subject
     * @return bool
     */
    public function isMatched(string $subject): bool
    {
        foreach ($this->criteria as $criterion) {
            if (strpos($subject, $criterion) !== FALSE) {
                return TRUE;
            }
        }

        return FALSE;
    }
}