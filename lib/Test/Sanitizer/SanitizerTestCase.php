<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SanitizerTestCase.php
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
 * @since      2019-04-27
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\Sanitizer;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Sanitizer\SanitizerInterface;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * SanitizerTestCase
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
abstract class SanitizerTestCase extends AbstractTestCase
{
    /**
     * @var SanitizerInterface
     */
    protected $sanitizer;

    /**
     * @var Provider
     */
    protected $provider;
}