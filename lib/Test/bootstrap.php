<?php

use RmpUp\WpDi\Test\Mirror;

const MY_PLUGIN_DIR = __DIR__;

class_alias(Mirror::class, '\\MyOwnActionListener');
class_alias(Mirror::class, '\\MyOwnCliCommand');
class_alias(Mirror::class, '\\MyOwnFilterHandler');
class_alias(Mirror::class, '\\MyOwnPostType');
class_alias(Mirror::class, '\\MyOwnShortcode');
class_alias(Mirror::class, '\\MyWidget');

class_alias(Mirror::class, '\\SomeRepository');
class_alias(Mirror::class, '\\SomeThing');
class_alias(Mirror::class, '\\SomeThingElse');
class_alias(Mirror::class, '\\WP_CLI');

// Tell wp-integration-test where to find WP
$_ENV['WP_DIR'] = $_ENV['WP_DIR'] ?: dirname(__DIR__, 2) . '/srv/';

require_once __DIR__ . '/../../vendor/pretzlaw/wp-integration-test/bootstrap.php';

class MyOwnWidget extends \WP_Widget {
    public function __construct($id = 'rmpup_myOwnWidget', $name = 'rmp-up test', $options = array(), $control = array())
    {
        parent::__construct((string) $id, $name, $options, $control);
    }
}
