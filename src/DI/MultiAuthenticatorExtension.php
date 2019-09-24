<?php declare(strict_types=1);

namespace Surda\MultiAuthenticator\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Surda\MultiAuthenticator\MultiAuthenticator;
use Surda\MultiAuthenticator\Resolver\AuthenticatorResolver;
use stdClass;

/**
 * @property-read stdClass $config
 */
class MultiAuthenticatorExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'authenticators' => Expect::arrayOf(
                Expect::anyOf(
                    Expect::string(),
                    Expect::type(Statement::class)
                )
            ),
            'rules' => Expect::arrayOf(
                Expect::array()
            ),
            'default' => Expect::anyOf(
                Expect::string(),
                Expect::type(Statement::class)
            ),
            'resolver' => Expect::anyOf(
                Expect::string(),
                Expect::type(Statement::class)
            )->default(AuthenticatorResolver::class),
            'authenticator' => Expect::anyOf(
                Expect::bool(),
                Expect::string(),
                Expect::type(Statement::class)
            )->default(MultiAuthenticator::class),
        ]);
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->config;

        $resolverDefinition = $builder->addDefinition($this->prefix('resolver'))
            ->setFactory($config->resolver, [$config->rules]);

        foreach ($config->authenticators as $type => $implementation) {
            if ($type === 'default' && $config->default !== NULL) {
                continue;
            }

            $authenticatorDefinition = $builder->addDefinition($this->prefix('authenticators.' . $type))
                ->setFactory($implementation)
                ->setAutowired(FALSE);

            $resolverDefinition->addSetup('addAuthenticator', [$type, $authenticatorDefinition]);

            if ($type === 'default') {
                $resolverDefinition->addSetup('setDefaultAuthenticator', [$authenticatorDefinition]);
            }
        }

        if ($config->default !== NULL) {
            $defaultAuthenticatorDefinition = $builder->addDefinition($this->prefix('authenticators.default'))
                ->setFactory($config->default)
                ->setAutowired(FALSE);
            $resolverDefinition->addSetup('setDefaultAuthenticator', [$defaultAuthenticatorDefinition]);
        }

        if ($config->authenticator !== FALSE) {
            $builder->addDefinition($this->prefix('authenticator'))
                ->setFactory($config->authenticator, [$builder->getDefinition($this->prefix('resolver'))]);
        }
    }
}