<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AutoResolvingProviderTest.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://mike-pretzlaw.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@mike-pretzlaw.de so we can mail you a copy.
 *
 * @package    wp-di
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-di
 * @since      2019-05-30
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test;

use ArrayObject;
use InvalidArgumentException;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\WpDi\Helper\WordPress\RegisterPostType;
use RmpUp\WpDi\LazyService;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Provider\WordPress\Actions;
use RmpUp\WpDi\Provider\WordPress\PostTypes;

/**
 * Introduction
 *
 *   Writing plugins can be a pain as everyone wants to do it different
 * and aim for different structures.
 * But what most of us have in common is that we want simplicity
 * and get away from WordPress to a more agnostic OOP workflow
 * to hold up testability, maintainability and all the other terms of software quality.
 *
 * We solve all of this using a service definition like this:
 *
 *
 * ```php
 * <?php // 'my-plugin-services.php'
 *
 * return [
 *   'options' => [
 *     'some_foo' => 'The default value of this option',
 *   ],
 *
 *   'services' => [
 *     SomeRepository::class,
 *
 *     SomeSeoStuff::class => [
 *       'arguments' => [
 *         SomeRepository::class,
 *         '%some_foo%',
 *       ],
 *
 *       'action' => 'template_redirect',
 *     ],
 *
 *     RejectUnauthorizedThings::class => [
 *       'action' => 'template_redirect',
 *     ],
 *   ]
 * ];
 *
 * ```
 *
 * Step after step this does:
 *
 * 1. Register a default value for the "some_foo" option
 * 2. Register the "SomeRepository"-service
 * 3. Register the "SomeSeoStuff"-service
 *    * Use the "SomeRepository"-service as first `__constructor` argument.
 *    * Use the "some_foo"-option as second argument.
 *    * Hook this service to the "template_redirect"-action
 * 4. Register the "RejectUnauthorizedThings"-service
 *    * Hook the service to the "template_redirect"-action
 *
 * So in case the `template_redirect` action is fired the services
 * "RejectUnauthorizedThings" and "SomeSeoStuff" will be lazy loaded
 * and invoked (e.g. using `__invoke`).
 *
 *   This is the very short syntax.
 * Read on to know more about the complete syntax
 * and other possibilities.
 *
 * We suggest splitting the configurations by their purpose
 * and load all at once into the (Pimple-)Container afterwards:
 *
 * ```php
 * <?php
 *
 * $container = new \Pimple\Container();
 *
 * $container->register(
 *   new \RmpUp\WpDi\Provider( require 'my-plugin-services.php' )
 * );
 * ```
 *
 * Hint: Using the commonly-known YAML-format
 * and service definitions makes it a bit easier to read.
 *
 * ```yaml
 * # my-plugin-services.yaml
 * options:
 *   some_foo: 'The default value of this option'
 *
 * services:
 *   SomeRepository: ~
 *
 *   SomeSeoStuff:
 *     arguments:
 *       - SomeRepository
 *       - '%some_foo%'
 *     action: template_redirect
 *
 *   RejectUnauthorizedThings:
 *     action: template_redirect
 * ```
 *
 * Forward this service-definition using some Yaml-Parser (e.g. `composer require symfony/yaml`):
 *
 * ```php
 * <?php
 *
 * $container = new \Pimple\Container();
 *
 * $container->register(
 *   new \RmpUp\WpDi\Provider(
 *     \Symfony\Component\Yaml\Yaml::parseFile( 'my-plugin-services.yaml' )
 *   )
 * );
 * ```
 *
 * @copyright  2020 Mike Pretzlaw (https://rmp-up.de)
 */
class AutoResolvingProviderTest extends AbstractTestCase
{
    use FilterAssertions;

    private $definition;

    protected function setUp()
    {
        remove_all_actions('template_redirect'); // TODO use ::truncateActions instead as soon as available

        $this->definition = $this->classComment()->execute(0);

        parent::setUp();

        $this->pimple->register(
            new Provider($this->definition)
        );
    }

    public function testActionsRegistered()
    {
        self::assertFilterHasCallback('template_redirect', new IsEqual(
            [new LazyService($this->container, 'SomeSeoStuff'), '__invoke']
        ));

        self::assertFilterHasCallback('template_redirect', new IsEqual(
            [new LazyService($this->container, \RejectUnauthorizedThings::class), '__invoke']
        ));
    }

    public function testServicesRegistered()
    {
        static::assertInstanceOf(\SomeSeoStuff::class, $this->container->get(\SomeSeoStuff::class));
        static::assertInstanceOf(\RejectUnauthorizedThings::class, $this->container->get(\RejectUnauthorizedThings::class));
    }

    public function testOptionDefaultVaue()
    {
        static::assertEquals('The default value of this option', get_option('some_foo'));
    }

    public function testThrowsExceptionWhenProviderInvalid()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->pimple->register(
            new Provider([uniqid('', true) => []])
        );
    }
}