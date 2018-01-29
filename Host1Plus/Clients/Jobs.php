<?php

namespace Host1Plus\Clients;

// interfaces/enums
use \Host1Plus\Interfaces\iClient;
use \Host1Plus\Interfaces\iTransport;
use \Host1Plus\Enums\{RequestMethods, Errors};

class Jobs extends aClient implements iClient
{
    private $ts;

    public function __construct(iTransport $transport)
    {
        $this->ts = $transport;
    }

    public function get(array $params)
    {
        $queryParams = [];
        $this->_parseParamsIntoQParams($params, $queryParams);

        return $this->ts->Request(RequestMethods::GET, 'jobs', $queryParams);
    }

    public function getOne(int $id, array $params)
    {
        if ($id <= 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'id', 'integer above 0', $id));

        $queryParams = [];
        $this->_parseParamsIntoQParams($params, $queryParams);

        return $this->ts->Request(RequestMethods::GET, "jobs/{$id}", $queryParams);
    }

    // private functions
    private function _parseParamsIntoQParams(array $params, array &$queryParams)
    {
        if (!empty($params['serviceId']))
            $queryParams['serviceId'] = $params['serviceId'];

        if (!empty($params['module']))
            $queryParams['module'] = $params['module'];

        if (!empty($params['instance']))
            $queryParams['instance'] = $params['instance'];

        if (!empty($params['action']))
            $queryParams['action'] = $params['action'];

        if (!empty($params['statusCode']))
            $queryParams['statusCode'] = $params['statusCode'];

        if (!empty($params['resultCode']))
            $queryParams['resultCode'] = $params['resultCode'];

        if (!empty($params['related']))
            $queryParams['related'] = $params['related'];

        if (!empty($params['page']))
            $queryParams['page'] = $params['page'];

        if (!empty($params['pagesize']))
            $queryParams['pagesize'] = $params['pagesize'];

        if (!empty($params['fields']))
            $queryParams['fields'] = $params['fields'];

        if (!empty($params['recurring']))
            $queryParams['recurring'] = $params['recurring'];

        if (!empty($params['disabled']))
            $queryParams['disabled'] = $params['disabled'];

        if (!empty($params['sort']))
            $queryParams['sort'] = $params['sort'];

        if (!empty($params['sortDesc']))
            $queryParams['sortDesc'] = $params['sortDesc'];
    }
}