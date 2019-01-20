<?php declare(strict_types=1);

namespace Surda\MultiAuthenticator;

use Nette\Security\IAuthenticator;

interface IResolvableAuthenticator extends IAuthenticator
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $subject
     * @return bool
     */
    public function isMatched(string $subject): bool;
}