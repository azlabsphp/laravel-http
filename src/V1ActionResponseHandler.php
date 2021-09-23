<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Drewlabs\Packages\Http\Traits\ActionResponseHandler as TraitsActionResponseHandler;
use Exception;

class V1ActionResponseHandler implements IActionResponseHandler
{

    use TraitsActionResponseHandler;

    public function ok($data, ?array $errors = null, $success = true)
    {
        return $this->respond(
            array(
                "data" => array(
                    'success' => $success,
                    'body' => array(
                        'error_message' => null,
                        'response_data' => $data,
                        'errors' => $errors
                    ),
                    'code' => 200,
                ),
            ),
            200
        );
    }

    public function error(Exception $e, ?array $errors = null)
    {
        app()['log']->error('HTTP ERROR : ' . $e->getMessage());
        return $this->respond(
            array(
                "data" => array(
                    'success' => false,
                    'body' => array(
                        'error_message' => filter_var(
                            config('app.debug'),
                            FILTER_VALIDATE_BOOLEAN
                        ) === false ?
                            'Server Error' : $e->getMessage(),
                        'response_data' => null, 'errors' => $errors
                    ),
                    'code' => 500,
                ),
            ),
            500
        );
    }

    public function badRequest(array $errors)
    {
        return $this->respond(
            array(
                "data" => array(
                    'success' => false,
                    'body' => array(
                        'error_message' => 'Bad request... Invalid request inputs',
                        'response_data' => null, 'errors' => $errors
                    ),
                    'code' => 422,
                ),
            ),
            422
        );
    }

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
        return $this->ok($data, $errors, $success);
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
        return $this->error($e, $errors);
    }
    /**
     * Method for converting bad request to a json formatted response
     *
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondBadRequest(array $errors)
    {
        return $this->badRequest($errors);
    }
}
