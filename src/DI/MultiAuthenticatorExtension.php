<?php declare(strict_types=1);

namespace Surda\MultiAuthenticator\DI;

use Nette\DI\CompilerExtension;

use Nette\Utils\Validators;
use Surda\MultiAuthenticator\MultiAuthenticator;
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;

class MultiAuthenticatorExtension extends CompilerExtension
{
    /** @var array */
    public $defaults = [
        'authenticators' => NULL,
        'default' => NULL,
        'autowired' => TRUE,
    ];

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        Validators::assertField($config, 'authenticators', 'array');
        Validators::assertField($config, 'autowired', 'bool');

        $resolver = $builder->addDefinition($this->prefix('resolver'))
            ->setFactory(AuthenticatorResolver::class);

        foreach ($config['authenticators'] as $type => $class) {
            $authenticator = $builder->addDefinition($this->prefix('authenticators.' . $type))
                ->setFactory($class)
                ->setAutowired(FALSE);
            $resolver->addSetup('addAuthenticator', [$authenticator]);
        }

        if ($config['default'] !== NULL) {
            $defaultAuthenticator = $builder->addDefinition($this->prefix('authenticator.default'))
                ->setFactory($config['default'])
                ->setAutowired(FALSE);
            $resolver->addSetup('setDefaultAuthenticator', [$defaultAuthenticator]);
        }

        $builder->addDefinition($this->prefix('authenticator'))
            ->setFactory(MultiAuthenticator::class, [$resolver])
            ->setAutowired($config['autowired']);
    }
}