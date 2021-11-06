<?php

namespace Drewlabs\Packages\Http\Controllers;

use Illuminate\Http\JsonResponse as Response;
use Illuminate\Http\Request;
use Drewlabs\Packages\Http\Contracts\IDataProviderControllerActionHandler;
use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Drewlabs\Packages\Http\Traits\LaravelOrLumenFrameworksApiController;
use Drewlabs\Contracts\Validator\Validator as ValidatorContract;
use Drewlabs\Packages\Http\ConfigurationManager;

/**
 * @package Drewlabs\Packages\Http
 */
class ApiDataProviderController
{
    use LaravelOrLumenFrameworksApiController;
    /**
     * Undocumented variable
     *
     * @var ValidatorContract
     */
    private $validator;

    /**
     *
     * @var IDataProviderControllerActionHandler
     */
    private $actionHandler;

    /**
     *
     * @var IActionResponseHandler
     */
    private $response;

    public function __construct(
        ValidatorContract $validator,
        IDataProviderControllerActionHandler $actionHandler,
        IActionResponseHandler $response
    ) {
        $this->middleware(ConfigurationManager::getInstance()->get("auth_middleware", 'auth'));
        $this->validator = $validator;
        $this->actionHandler = $actionHandler;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @route GET /{collection}[/{$id}]
     *
     * @param Request $request
     * @param string $collection
     * @param string|int|null $id
     *
     * @return Response
     */
    public function index(Request $request, $collection, ...$parameters)
    {
        $fn_params = \array_filter(func_get_args(), \drewlabs_core_filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    ConfigurationManager::getInstance()->get("requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply validation rules to the request body
            $errors = $this->actionHandler
                ->applyValidationHandler(ConfigurationManager::getInstance()->get("requests.$collection.actions.index.validateRequestBody"), $request, $this->validator, $fn_params);
            if (!is_null($errors) && count($errors) > 0) {
                return $this->response->badRequest($errors);
            }
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(ConfigurationManager::getInstance()->get("requests.$collection.actions.index.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->response->unauthorized($request);
            }
            // Apply filters rules
            $filters = array(
                'orderBy' => ($request->has('order') && $request->has('by')) ?
                    array('order' => $request->get('order'), 'by' => $request->get('by')) :
                    array('order' => 'desc', 'by' => 'updated_at'),
            );
            // Parse request query parameters
            $filters = array_merge($filters, $this->actionHandler
                ->applyQueryBuilder(ConfigurationManager::getInstance()->get("requests.$collection.actions.index.queryBuilder"), $request, $fn_params));
            $result = $provider->get(
                $filters,
                ['*'],
                ConfigurationManager::getInstance()->get("requests.$collection.actions.index.relationQuery", true),
                $request->has('page'),
                $request->get('per_page')
            );
            return $this->response->ok(
                $this->actionHandler->applyTransformResponseBody(
                    ConfigurationManager::getInstance()->get("requests.$collection.actions.index.transformResponseBody"),
                    $result,
                    $fn_params
                )
            );
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @route POST /{collection}/
     *
     * @param Request $request
     * @param string $collection
     *
     * @return Response
     */
    public function store(Request $request, $collection)
    {
        $fn_params = \array_filter(func_get_args(), \drewlabs_core_filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    ConfigurationManager::getInstance()->get("requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply request body data transformation handler to the request inputs
            $data = $this->actionHandler
                ->applyTransformRequestBody(ConfigurationManager::getInstance()->get("requests.$collection.actions.store.transformRequestBody"), $request, $fn_params);
            // Apply validation rules to the request body
            $request  = $request->merge($data);
            $errors = $this->actionHandler
                ->applyValidationHandler(ConfigurationManager::getInstance()->get("requests.$collection.actions.store.validateRequestBody"), $request, $this->validator, $fn_params);
            if (!is_null($errors) && count($errors) > 0) {
                return $this->response->badRequest($errors);
            }
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(ConfigurationManager::getInstance()->get("requests.$collection.actions.store.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->response->unauthorized($request);
            }
            $handlerParams = $this->actionHandler
                ->applyBuildProviderHandlerParams(ConfigurationManager::getInstance()->get("requests.$collection.actions.store.providerHandlerParam"), $data, $request);
            $result =  $provider->create($data, $handlerParams);
            return $this->response->ok(
                array(
                    'data' => $this->actionHandler->applyTransformResponseBody(
                        ConfigurationManager::getInstance()->get("requests.$collection.actions.store.transformResponseBody"),
                        $result,
                        $fn_params
                    )
                )
            );
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->response->error($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @route GET /{collection}/{$id}
     *
     * @param Request $request
     * @param string $collection
     * @param array $param
     *
     * @return Response
     */
    public function show(Request $request, $collection, ...$param)
    {
        $fn_params = \array_filter(func_get_args(), \drewlabs_core_filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    ConfigurationManager::getInstance()->get("requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(ConfigurationManager::getInstance()->get("requests.$collection.actions.show.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->response->unauthorized($request);
            }
            $query = $this->actionHandler
                ->applyQueryBuilder(ConfigurationManager::getInstance()->get("requests.$collection.actions.show.queryBuilder"), $request, $fn_params);
            $result =  $provider->get($query);
            return $this->response->ok(
                array(
                    'data' => $this->actionHandler->applyTransformResponseBody(
                        ConfigurationManager::getInstance()->get("requests.$collection.actions.show.transformResponseBody"),
                        $result,
                        $fn_params
                    )
                )
            );
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->response->error($e);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @route UPDATE /{collection}/{$id}
     *
     * @param Request $request
     * @param string $collection
     * @param array $param
     *
     * @return Response
     */
    public function update(Request $request, $collection, ...$param)
    {
        $fn_params = \array_filter(func_get_args(), \drewlabs_core_filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    ConfigurationManager::getInstance()->get("requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply request body data transformation handler to the request inputs
            $data = $this->actionHandler
                ->applyTransformRequestBody(ConfigurationManager::getInstance()->get("requests.$collection.actions.update.transformRequestBody"), $request, $fn_params);
            // Apply validation rules to the request body
            $errors = $this->actionHandler
                ->applyValidationHandler(ConfigurationManager::getInstance()->get("requests.$collection.actions.update.validateRequestBody"), $request, $this->validator, $fn_params);
            if (!is_null($errors) && count($errors) > 0) {
                return $this->response->badRequest($errors);
            }
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(ConfigurationManager::getInstance()->get("requests.$collection.actions.update.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->response->unauthorized($request);
            }
            // Return success response to user
            $query = $this->actionHandler
                ->applyQueryBuilder(
                    ConfigurationManager::getInstance()->get("requests.$collection.actions.update.queryBuilder"),
                    $request,
                    $fn_params
                );
            $handlerParams = $this->actionHandler
                ->applyBuildProviderHandlerParams(ConfigurationManager::getInstance()->get("requests.$collection.actions.update.providerHandlerParam"), $data, $request);
            $result =  $provider->modify($query, $data, $handlerParams);
            return $this->response->ok(
                array(
                    'data' => $this->actionHandler->applyTransformResponseBody(
                        ConfigurationManager::getInstance()->get("requests.$collection.actions.update.transformResponseBody"),
                        $result,
                        $fn_params
                    )
                )
            );
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->response->error($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @route DELETE /{collection}/{$id}
     *
     * @param Request $request
     * @param string $collection
     * @param array $param
     *
     * @return Response
     */
    public function destroy(Request $request, $collection, ...$param)
    {
        $fn_params = \array_filter(func_get_args(), \drewlabs_core_filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    ConfigurationManager::getInstance()->get("requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(ConfigurationManager::getInstance()->get("requests.$collection.actions.destroy.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->response->unauthorized($request);
            }
            $query = $this->actionHandler
                ->applyQueryBuilder(ConfigurationManager::getInstance()->get("requests.$collection.actions.destroy.queryBuilder"), $request, $fn_params);
            $result =  $provider->delete($query, ConfigurationManager::getInstance()->get("requests.$collection.actions.destroy.massDelete"));
            return $this->response->ok(
                array(
                    'data' => $this->actionHandler->applyTransformResponseBody(
                        ConfigurationManager::getInstance()->get("requests.$collection.actions.destroy.transformResponseBody"),
                        $result,
                        $fn_params
                    )
                )
            );
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
