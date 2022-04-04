<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Drewlabs\Packages\Http\Traits\ResponseHandler;
use Drewlabs\Packages\Http\Traits\BinaryResponseHandler;
use Drewlabs\Packages\Http\Traits\ContainerAware;
use Drewlabs\Packages\Http\Traits\UnAuthorizedResponseHandler;

class JsonApiResponseHandler implements IActionResponseHandler
{
    use ResponseHandler;
    use BinaryResponseHandler;
    use UnAuthorizedResponseHandler;
    use ContainerAware;

    /**
     * Method for converting thrown exceptions into http response
     *
     * @param mixed $data
     * @param array|null $errors
     * @param bool $success
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondOk($data, array $errors = null, $success = true)
    {
        return $this->withStatus(200)->ok($data, $errors, $success);
    }
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param \Exception $e
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondError(\Exception $e, array $errors = null)
    {
        return $this->withStatus(500)->error($e, $errors);
    }
    /**
     * Method for converting bad request to a json formatted response
     *
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondBadRequest(array $errors)
    {
        return $this->withStatus(400)->badRequest($errors);
    }


    /**
     * Return an HTTP JSON response with status >=200 AND <=204
     *
     * @param mixed $data
     * @param array|null $errors
     * @param bool $success
     * @return Response
     */
    public function ok($data, array $errors = null, $success = true)
    {
        return $this->respond($data, $this->status_code ?? 200);
    }

    /**
     * Return a Server Error HTTP response  with status 500
     *
     * @param \Exception $e
     * @param array|null $errors
     * @return Response
     */
    public function error(\Exception $e, array $errors = null)
    {
        $response_message = $e->getMessage();
        // Check to see if the error is not log by laravel
        self::createResolver('log')()->error(
            sprintf(
                '%s : %s',
                ConfigurationManager::getInstance()->get('app.env', 'local'),
                $response_message
            )
        );
        // Checks if running in production or dev for error message to be send
        return $this->respond(
            filter_var(ConfigurationManager::getInstance()->get('app.debug', true), FILTER_VALIDATE_BOOLEAN) === false ?
                'Server Error' : $response_message,
            $this->status_code ?? 500
        );
    }

    /**
     * Return an HTTP Bad Request response  with status >=400 or <=403 or 422
     *
     * @param array $errors
     * @return Response
     */
    public function badRequest(array $errors)
    {
        return $this->respond($errors, $this->status_code ?? 422);
    }
}
