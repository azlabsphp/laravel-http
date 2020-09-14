<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;

class ActionResponseHandler implements IActionResponseHandler
{
    use \Drewlabs\Packages\Http\Traits\ActionResponseHandler;

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
        return $this->respond(
            array(
                'success' => $success,
                'body' => array('error_message' => null, 'response_data' => $data, 'errors' => $errors),
                'code' => 200,
            ),
            200
        );
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
        $response_message = $e->getMessage();
        app()['log']->error('API_ERROR_MESSAGE : ' . $e->getMessage());
        // Checks if running in production or dev for error message to be send
        return $this->respond(
            array(
                'success' => false,
                'body' => array(
                    'error_message' => filter_var(config('app.debug'), FILTER_VALIDATE_BOOLEAN) === false ?
                        'Server Error' : $response_message,
                    'response_data' => null, 'errors' => $errors
                ),
                'code' => 500,
            ),
            500
        );
    }
    /**
     * Method for converting bad request to a json formatted response
     *
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondBadRequest(array $errors)
    {
        $response_message = 'Bad request... Invalid request inputs';
        return $this->respond(
            array(
                'success' => false,
                'body' => array('error_message' => $response_message, 'response_data' => null, 'errors' => $errors),
                'code' => 422,
            ),
            422
        );
    }
}
