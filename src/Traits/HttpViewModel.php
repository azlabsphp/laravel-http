<?php

namespace Drewlabs\Packages\Http\Traits;

use Closure;
use Drewlabs\Contracts\Validator\Validator;
use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Functional;
use Drewlabs\Core\Helpers\Str;
use Generator;
use HasServerRequest;
use RuntimeException;

trait HttpViewModel
{
    use \Drewlabs\Core\Validator\Traits\ViewModel;
    use ContainerAware;
    use HasServerRequest;
    //#region Miscellanous
    /**
     * Validates the view model object using the bounded validator {@see Validator} instance.
     *
     * if the view model provides updateRules(), passing $updating=true will load the update
     * rules
     *
     * ```php
     * <?php
     * $viewmodel = new ViewModelClass($request);
     *
     * // Validating
     * $viewmodel = $viewmodel->validated();
     *
     * // To execute a method after validating the model
     * $viewmodel->validated(function() use ($viewmodel) {
     *  // Persist data to database after validation
     * });
     *
     * // In order to use update rules
     * // This will throw an exception if the validation fails
     * $viewmodel->validated(true);
     *
     * // In order to use the update rules and pass a callback
     * // which runs when validation passes
     * $viewmodel->validated(true, function() use ($viewmodel) {
     *  // Persist data to database after validation
     * });
     * ```
     * @param bool|\Closure|null $updating
     * @param \Closure|null $callback
     * @throws \Drewlabs\Core\Validator\Exceptions\ValidationException
     * @return self
     */
    public function validated(...$args)
    {
        $argCount = count($args);
        if ($argCount === 1 && Functional::isCallable($args[0])) {
            return $this->validateWithCallback($this->createValidator(), $args[0]);
        } else if ($argCount === 1 && boolval($args[0]) === true) {
            return $this->validate($this->createValidator(true));
        } else if ($argCount > 1) {
            return $this->validate($this->createValidator(true), $args[1]);
        } else {
            return $this->validate($this->createValidator());
        }
    }

    /**
     * Internaly creates validator instance
     * 
     * @return Validator
     */
    private function createValidator(bool $updating = false)
    {
        /**
         * @var Validator
         */
        $validator = self::createResolver(Validator::class)();
        return $updating ? $validator->updating() : $validator;
    }

    /**
     * Validates the current instance with a callback function
     * 
     * @param Validator $validator 
     * @param Closure $callback 
     * @return Validator 
     */
    private function validateWithCallback(Validator $validator, \Closure $callback)
    {
        return $validator->validate($this, $callback);
    }

    /**
     * Validates the current instance and throws exception if the validation fails
     * 
     * @param Validator $validator 
     * @return self 
     * 
     * @throws RuntimeException
     * @throws \Drewlabs\Validator\Exceptions\ValidationException|\Drewlabs\Core\Validator\Exceptions\ValidationException
     */
    private function validate(Validator $validator)
    {
        $validator = $validator->validate($this);
        if ($validator->fails()) {
            $deprecatedException = \Drewlabs\Core\Validator\Exceptions\ValidationException::class;
            $exception = \Drewlabs\Validator\Exceptions\ValidationException::class;
            if (class_exists($deprecatedException)) {
                throw new $deprecatedException($validator->errors());
            } else if (class_exists($exception)) {
                throw new $exception($validator->errors());
            } else {
                throw new RuntimeException('Failed validating view model instance');
            }
        }
        return $this;
    }
    //#endregion


    /**
     * Creates a new view model instance from attributes
     *
     * @param array $attributes
     * @return self
     */
    public static function new(array $attributes = [])
    {
        return (new static)->merge($attributes ?? []);
    }

    /**
     * Creates a fluent rules by applying a prefix to rules keys
     *
     * @param string|null $prefix
     * @param array $attributes
     * @return mixed
     */
    public static function createRules(string $prefix = null, array $attributes = [])
    {
        return  null === $prefix ?
            static::new($attributes ?? [])->rules() :
            static::createRules_(static::new($attributes ?? [])->rules(), $prefix);
    }

    /**
     * Creates a fluent update rules by applying a prefix to rules keys
     *
     * @param string|null $prefix
     * @param array $attributes
     * @return mixed
     */
    public static function createUpdateRules(string $prefix = null, array $attributes = [])
    {

        return null === $prefix ?
            static::new($attributes ?? [])->updateRules() :
            static::createRules_(static::new($attributes ?? [])->updateRules(), $prefix);
    }

    /**
     * Internal method for creating rules using the provided prefix
     *
     * @param array $rules
     * @param string|null $prefix
     * @return array
     */
    private static function createRules_(array $rules, string $prefix = null)
    {
        return Arr::create(static::prefixRules($rules, $prefix));
    }

    /**
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @param string $prefix 
     * @return string 
     */
    private static function reconstructRequiredRules($key, $value, string $prefix)
    {
        $values = array_map(function ($item) use ($prefix) {
            return "$prefix.$item";
        }, array_filter(explode(',', Str::after("$key:", $value)), function ($item) {
            return !empty($item);
        }));
        return "$key:" . implode(',', $values);
    }

    /**
     * Produces an iterable of prefixed rules
     * 
     * @param array $rules 
     * @param string|null $prefix 
     * @return Generator<string, mixed, mixed, void> 
     */
    private static function prefixRules(array $rules, string $prefix = null)
    {
        foreach ($rules as $key => $value) {
            $value = array_map(function ($current) use ($prefix) {
                if (!is_string($current)) {
                    return $current;
                }
                if (false !== strpos($current, 'required_without:')) {
                    return static::reconstructRequiredRules('required_without', $current, $prefix);
                }
                if (false !== strpos($current, 'required_without_all:')) {
                    return static::reconstructRequiredRules('required_without_all', $current, $prefix);
                }
                if (false !== strpos($current, 'required_with:')) {
                    return static::reconstructRequiredRules('required_with', $current, $prefix);
                }
                if (false !== strpos($current, 'required_with_all:')) {
                    return static::reconstructRequiredRules('required_with_all', $current, $prefix);
                }
                return $current;
            }, is_string($value) ? explode('|', $value) : $value);
            yield "$prefix.$key" => $value;
        }
    }
}
