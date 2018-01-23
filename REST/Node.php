<?php

namespace REST;

// enums
use \REST\Enums\{AccessLevels, Errors};

// Exceptions
use \REST\Exceptions\{NotFound, HandlerError};

// utilities
use \Utilities\Header;

class Node
{
    private $path;
    private $pathFull;
    private $handler;

    private $children;
    private $data;
    private $pathParams;
    private $queryParams;
    private $body;

    private $initialized;
    private $accessLevels;

    private $hasWildChild;
    private $wildChild;

    private $requestIp;

    public function __construct()
    {
        $this->children = $this->data = $this->pathParams = $this->queryParams = $this->body = $this->accessLevels = [];
        $this->hasWildChild = false;
    }

    // getters

    function Child($path)
    {
        if (!$this->childExists($path))
            throw new NotFound( \sprintf(Errors::NotFound, 'node child') );

        return $this->children[$path];
    }

    function WildChild()
    {
        if (!$this->hasWildChild)
            throw new NotFound( \sprintf(Errors::NotFound, 'node wild child') );

        return $this->wildChild;
    }

    function Path()
    {
        return $this->path;
    }

    function PathFull()
    {
        return $this->pathFull;
    }

    public function QueryParams()
    {
        return $this->queryParams;
    }

    public function PathParams()
    {
        return $this->pathParams;
    }

    public function Body()
    {
        return $this->body;
    }

    public function RequestIp()
    {
        return $this->requestIp;
    }

    public function AccessLevels()
    {
        return $this->accessLevels;
    }

    public function getData($key)
    {
        if (!\array_key_exists($key, $this->data))
            throw new NotFound( \sprintf(Errors::NotFound, "item for key: {$key}") );

        return $this->data[$key];
    }

    public function getQueryParam($key)
    {
        if (!\array_key_exists($key, $this->queryParams))
            return null;

        return $this->queryParams[$key];
    }

    // setters

    function setPath(string $path)
    {
        $this->path = $path;
    }

    function setPathFull(string $pathFull)
    {
        $this->pathFull = $pathFull;
    }

    function setHandler($handler)
    {
        $this->handler = $handler;
        $this->initialized = true;
    }

    public function setQueryParams(array $params)
    {
        $this->queryParams = $params;
    }

    public function setPathParams($pathParams)
    {
        $this->pathParams = $pathParams;
    }

    public function setBody(array $body)
    {
        $this->body = $body;
    }

    public function setRequestIp($requestIp)
    {
        $this->requestIp = $requestIp;
    }

    public function setAccessLevels(array $accessLevels)
    {
        $this->accessLevels = $accessLevels;
    }

    function addChild(string $path, Node $node)
    {
        $this->children[$path] = $node;
    }

    public function addData($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function addPathParam($key, $value)
    {
        $this->pathParams[$key] = $value;
    }

    // logic

    function childExists($path)
    {
        return isset($this->children[$path]);
    }

    function makeWildChild($path)
    {
        $this->wildChild = new Node();
        $this->wildChild->setPath($path);
        $this->hasWildChild = true;
    }

    function hasWildChild()
    {
        return $this->hasWildChild;
    }

    public function executeHandler()
    {
        if (!$this->initialized)
            throw new NotFound( \sprintf(Errors::NotFound, "API request node") );

        if (!\is_callable($this->handler))
            throw new \InvalidArgumentException( \sprintf(Errors::InvalidArgument, 'API request handler', 'callable function', $this->handler) );

        list($data, $headerCode) = \call_user_func_array($this->handler, [$this]);
        if ($data === false)
            throw new HandlerError;

        if (empty($headerCode))
            $headerCode = Header::OK;

        return ['data' => $data, 'headerCode' => $headerCode];
    }

    public function isPublic()
    {
        return \in_array(AccessLevels::Pub, $this->accessLevels);
    }

    public function isUser()
    {
        return \in_array(AccessLevels::User, $this->accessLevels);
    }

    public function isReseller()
    {
        return \in_array(AccessLevels::Reseller, $this->accessLevels);
    }

    public function isAdmin()
    {
        return \in_array(AccessLevels::Admin, $this->accessLevels);
    }

    public function isValidAccessLevel($accessLevel)
    {
        return \in_array($accessLevel, $this->accessLevels);
    }

    public function prepSearchParams(array $validParams, $pagination = false)
    {
        $search = [];
        if (\count($this->queryParams) == 0)
            return $search;

        foreach ($this->queryParams as $key => $value)
        {
            if (!isset($validParams[$key]))
                continue;

            if ($validParams[$key]['multi'] && \strpos($this->queryParams[$key], ',') != false)
                $search[$key] = \explode(',', $value);
            else
                $search[$key] = $value;
        }

        if ($pagination && isset($this->queryParams['page']) && isset($this->queryParams['pagesize']))
        {
            $search['page']     = $this->queryParams['page'];
            $search['pagesize'] = $this->queryParams['pagesize'];
        }

        return $search;
    }

    public function prepFields(array $validFields)
    {
        if (empty($this->queryParams['fields']))
            return $validFields;

        return \array_intersect(\explode(',', $this->queryParams['fields']), $validFields);
    }

    public function prepSortParams()
    {
        $sort = [];

        if (isset($this->queryParams['sort']))
            $sort['by'] = $this->queryParams['sort'];

        if (isset($this->queryParams['sortDesc']) && $this->queryParams['sortDesc'] === "1")
            $sort['desc'] = true;

        return $sort;
    }

    public function parseIntoQueryParams($query)
    {
        if (!empty($query))
            \parse_str($query, $this->queryParams);
    }

    public function parseBodyFromInputJson()
    {
        $input = \file_get_contents('php://input');
        if (!empty($input))
            $this->setBody( \GuzzleHttp\json_decode($input, true) );
    }
}