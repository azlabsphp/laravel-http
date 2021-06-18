<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Core\Support\Traits\ImmutableConfigurationManager;

class ConfigurationManager
{

    use ImmutableConfigurationManager;

    /**
     * Create and initialize the configuration from an array
     *
     * @param array $config
     * @return static
     */
    public static function configure(array $config)
    {
        $self = drewlabs_core_create_attribute_setter('config', $config ?? [])(new static);
        static::$instance = $self;
        return $self;
    }
}