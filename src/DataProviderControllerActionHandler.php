<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Contracts\Data\DataProviderInterface;
use Drewlabs\Packages\Http\Contracts\IDataProviderControllerActionHandler;
use Drewlabs\Contracts\Validator\Validator as ValidatorContract;

/**
 * @package Drewlabs\Packages\Http
 */
class DataProviderControllerActionHandler implements IDataProviderControllerActionHandler
{
    /**
     * Data provider instance
     *
     * @var DataProviderInterface
     */
    private $provider;


    /**
     * {@inheritDoc}
     */
    public function bindProvider($request, $callback, $params)
    {
        $this->provider = \build_data_provider($callback, $params);
        // Checks if the provider instance has been set
        if (is_null($this->provider)) {
            throw new \Drewlabs\Packages\Http\Exceptions\BadProviderDeclarationException($request);
        }
        if (!($this->provider instanceof DataProviderInterface)) {
            throw new \Drewlabs\Packages\Http\Exceptions\BadProviderDeclarationException($request, "Constructed provider is not an instance of " . DataProviderInterface::class);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * {@inheritDoc}
     */
    public function applyQueryBuilder($callback, $request, $params = [])
    {
        if (is_array($callback)) {
            return $callback;
        }
        if ($callback instanceof \Closure) {
            return $callback(...$params);
        }
        throw new \Drewlabs\Packages\Http\Exceptions\QueryBuilderHandlerException($request);
    }

    /**
     * {@inheritDoc}
     */
    public function applyGatePolicyHandler($callback, $request, DataProviderInterface $provider, $params = [])
    {
        if (is_null($callback)) {
            return true;
        }
        if (!($callback instanceof \Closure) && is_bool($callback)) {
            return filter_var($callback, FILTER_VALIDATE_BOOLEAN);
        }
        if ($callback instanceof \Closure) {
            return $callback($provider, ...$params);
        }
        throw new \Drewlabs\Packages\Http\Exceptions\PolicyHandlerException($request);
    }

    /**
     * {@inheritDoc}
     */
    public function applyTransformRequestBody($callback, $request, $params = [])
    {
        if (is_null($callback)) {
            return $request->all();
        }
        if ($callback instanceof \Closure) {
            return $callback(...$params);
        } //
        throw new \Drewlabs\Packages\Http\Exceptions\TransformRequestBodyException($request);
    }

    /**
     * {@inheritDoc}
     */
    public function applyValidationHandler($callback, $request, ValidatorContract $validator, $params = [])
    {
        if (is_null($callback)) {
            return [];
        }
        if ($callback instanceof \Closure) {
            return $callback($validator, ...$params);
        }
        throw new \Drewlabs\Packages\Http\Exceptions\RequestValidationException($request);
    }


    /**
     * {@inheritDoc}
     */
    public function applyBuildProviderHandlerParams($callback, $values = [], $request = null)
    {
        if (is_null($callback)) {
            return [];
        }
        if ($callback instanceof \Closure) {
            $result = $callback($values, $request);
            return is_null($result) ? [] : $result;
        }
        return $callback;
    }

    /**
     * Apply data transformation callback to the data provider query result
     *
     * @param \Closure|callable|null $callback
     * @param array|mixed $body
     * @param array $params
     * @return array|mixed
     */
    public function applyTransformResponseBody($callback, $body, $params = [])
    {
        if ($callback instanceof \Closure) {
            $result = $callback($body, ...$params);
            return $result;
        }
        return $body;
    }
}
