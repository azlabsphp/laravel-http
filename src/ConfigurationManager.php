<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Laravel\Http;

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Reflector;

class ConfigurationManager
{
    /**
     * Static class instance.
     *
     * @var self
     */
    private static $instance;

    /**
     * Configurations cache property.
     *
     * @var array
     */
    private $config;

    /**
     * Private constructor to prevent users from calling new on the current class.
     */
    private function __construct()
    {
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            $self = new static();
            $self = Reflector::propertySetter('config', $self->config ?? [])($self);
            static::$instance = $self;
        }

        return static::$instance;
    }

    public function get($key = null, $default = null)
    {
        if (null === $key) {
            return array_merge($this->config ?? [], []);
        }
        $value = Arr::get($this->config, $key, $default);

        return null === $value ? ($default instanceof \Closure ? $default() : $default) : $value;
    }

    public function offsetExists($offset)
    {
        return null !== Arr::get($offset, null);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset, null);
    }

    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Configuration manager class is a readonly class, operations changing the class state are not allowed');
    }

    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Configuration manager class is a readonly class, operations changing the class state are not allowed');
    }

    /**
     * Create and initialize the configuration from an array.
     *
     * @return static
     */
    public static function configure(array $config)
    {
        $self = Reflector::propertySetter('config', $config ?? [])(new static());
        static::$instance = $self;

        return $self;
    }
}
