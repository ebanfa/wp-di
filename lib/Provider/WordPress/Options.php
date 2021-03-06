<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Options.php
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
 * @since      2019-06-08
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Provider\WordPress;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RmpUp\WpDi\Helper\WordPress\OptionsResolver;
use RmpUp\WpDi\ServiceDefinition\OptionNode;

/**
 * Options
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-08
 */
class Options implements ServiceProviderInterface
{
    /**
     * @var array
     */
    private $serviceDefinition;

    public function __construct(array $serviceDefinition)
    {
        $this->serviceDefinition = $serviceDefinition;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple): void
    {
        $optionResolver = new OptionsResolver(new \Pimple\Psr11\Container($pimple));

        foreach ($this->serviceDefinition as $optionKey => $value) {
            if (!is_callable($value)) {
                $value = new OptionNode($optionKey, $value);
            }

            $pimple[(string) $optionKey] = $value; // DEPRECATED 0.7
            $pimple['%' . $optionKey . '%'] = $value;

            add_filter('default_option_' . $optionKey, $optionResolver, 10, 3);
        }
    }
}