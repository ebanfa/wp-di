<?php


namespace RmpUp\WpDi\Test\Services;

use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * Full service definition
 *
 * Whatever you do,
 * all definitions and shortcuts will be parsed
 * and turn into this format:
 *
 * ```php
 * <?php
 *
 * return [
 *   'service_name' => [
 *     'class' => SomeThing::class,
 *     'arguments' => [
 *       1,
 *       2,
 *       'and more',
 *     ],
 *     // 'other_things' => ...
 *   ],
 * ]
 * ```
 *
 * So for example a overly simplified definitions like this:
 *
 * ```php
 * <?php
 *
 * return [
 *   SomeThing::class => [
 *     'one',
 *     2,
 *     Three::class
 *   ],
 *
 *   Three::class,
 * ];
 * ```
 *
 * Will turn into the more complete definition:
 *
 * ```php
 * <?php
 *
 * return [
 *   'SomeThing' => [
 *     'class' => 'SomeThing',
 *     'arguments' => [
 *       'one',
 *       2,
 *       'Three'
 *     ],
 *   ],
 *
 *   'Three' => [
 *     'class' => 'Three',
 *     'arguments' => [],
 *   ],
 * ];
 * ```
 *
 * @package RmpUp\WpDi\Test\Services
 */
class WuFullServiceDefinitionTest extends SanitizerTestCase
{
    private const SHORT_EXAMPLE = 1;
    private const COMPLETE_EXAMPLE = 2;

    protected function setUp()
    {
        parent::setUp();

        $this->services = $this->classComment()->execute(self::SHORT_EXAMPLE);

        $this->sanitizer = new \RmpUp\WpDi\Sanitizer\Services();

        $this->pimple->register(
            new Services($this->sanitizer->sanitize($this->services))
        );
    }

    public function testCompletedServiceDefinition()
    {
        static::assertEquals(
            $this->classComment()->execute(self::COMPLETE_EXAMPLE),
            $this->sanitizer->sanitize($this->services)
        );
    }
}
