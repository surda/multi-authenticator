# MultiAuthenticator

## Installation

The recommended way to is via Composer:

```
composer require surda/multi-authenticator
```

After that you have to register extension in config.neon:

```yaml
extensions:
    multiAuthenticator: Surda\MultiAuthenticator\DI\MultiAuthenticatorExtension
```

## Minimal configuration

```yaml
multiAuthenticator:
    authenticators:
        - MyLdapAuthenticator
        - MyDatabaseAuthenticator
        - ...
```

List of all configuration options:

```yaml
multiAuthenticator:
    authenticators:
        - MyLdapAuthenticator
        - MyDatabaseAuthenticator
        - ...
    default: MyDefaultAuthenticator
    autowired: true # MultiAuthenticator::class
```

## Using via MultiAuthenticator (default)

Autowired MultiAuthenticator class

```php
try {
    $this->user->login($values->username, $values->password);
} catch (Nette\Security\AuthenticationException $e) {
    // ...
}
```

## Using via resolver

```php
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;

/** @var AuthenticatorResolver */
private $resolver;

/**
 * @param AuthenticatorResolver $resolver
 */
public function __construct(AuthenticatorResolver $resolver)
{
    $this->resolver = $resolver;
}

try {
    $this->user->setAuthenticator($this->resolver->resolveByUsername('ad.domain.com\myusername'));
    // or
    $this->user->setAuthenticator($this->resolver->resolveByType('ldap'));
    
    try {
        $this->user->login($values->username, $values->password);
    } catch (Nette\Security\AuthenticationException $e) {
        // ...
    }
}
catch (AuthenticatorNotFoundException $e) {
    // ...
}
```
