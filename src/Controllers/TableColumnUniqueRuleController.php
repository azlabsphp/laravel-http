<?php

namespace Drewlabs\Packages\Http\Controllers;

use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\DatabaseManager;

class TableColumnUniqueRuleController
{
    /**
     * 
     * @var IActionResponseHandler
     */
    private $response;

    /**
     * 
     * @var DatabaseManager
     */
    private $db;

    public function __construct(IActionResponseHandler $response)
    {
        $this->response = $response;
        $this->db = Container::getInstance()->make('db');
    }

    /**
     * Handle POST /is_unique
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $property = $request->has('property') ? $request->get('property') : null;
        $value = $request->get('value');
        $table = $request->has('entity') ? $request->get('entity') : null;
        if ((null === $property) || (null === $table)) {
            return $this->response->badRequest(
                [
                    'property' => ['proterty field is required'],
                    'entity' => ['entity field is required']
                ]
            );
        }
        try {
            $result = $this->db->connection()
                ->table($table)
                ->where($property, $value)
                ->get();
            if ($result->isNotEmpty()) {
                return $this->response->badRequest([
                    'unique' => false
                ]);
            }
            return $this->response->ok([
                'unique' => true
            ]);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
