<?php

namespace REST;

use \REST\Enums\Errors;
use \REST\Exceptions\NotFound;

class Router
{
    private $tree;
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->tree = [
            'GET'    => new Node(),
            'POST'   => new Node(),
            'PATCH'  => new Node(),
            'PUT'    => new Node(),
            'DELETE' => new Node(),
        ];
    }

    public function addRoute(string $method, string $path, $handler, array $accessLevels = [])
    {
        if (!isset($this->tree[$method]))
            throw new \InvalidArgumentException( \sprintf(Errors::InvalidArgument, 'method', 'GET,POST,PATCH,PUT,DELETE', $method) );

        if ($path == '' || $path[0] != '/')
            throw new \InvalidArgumentException( \sprintf(Errors::InvalidArgument, 'path', 'string with prefix of \'/\'', $path) );

        /* @var $node Node */
        $node = $this->tree[$method];

        if ($path != '/')
        {
            $splitPath = \explode('/', $path);
            $splitLen  = \count($splitPath);
            for ($i = 1; $i < $splitLen; $i++)
            {
                $p = $splitPath[$i];

                if ($node->childExists($p))
                {
                    $node = $node->Child($p);
                }
                elseif ($p[0] == ':')
                {
                    if (!$node->hasWildChild())
                    {
                        $node->makeWildChild($p);
                    }

                    $node = $node->WildChild();
                }
                else
                {
                    $n = new Node();
                    $n->setPath($p);
                    $node->addChild($p, $n);
                    $node = $n;
                }
            }
        }

        foreach ($this->data as $key => $value)
        {
            $node->addData($key, $value);
        }

        $node->setAccessLevels($accessLevels);
        $node->setPathFull($path);
        $node->setHandler($handler);
    }

    public function GET(string $path, $handler, array $accessLevels = [])
    {
        $this->addRoute('GET', $path, $handler, $accessLevels);
    }

    public function POST(string $path, $handler, array $accessLevels = [])
    {
        $this->addRoute('POST', $path, $handler, $accessLevels);
    }

    public function PATCH(string $path, $handler, array $accessLevels = [])
    {
        $this->addRoute('PATCH', $path, $handler, $accessLevels);
    }

    public function PUT(string $path, $handler, array $accessLevels = [])
    {
        $this->addRoute('PUT', $path, $handler, $accessLevels);
    }

    public function DELETE(string $path, $handler, array $accessLevels = [])
    {
        $this->addRoute('DELETE', $path, $handler, $accessLevels);
    }

    /**
     *
     * @param type $method
     * @param type $path
     * @return Node
     * @throws InvalidArgument
     * @throws NotFound
     */
    public function findNode(string $method, string $path)
    {
        if (!isset($this->tree[$method]))
            throw new \InvalidArgumentException( \sprintf(Errors::InvalidArgument, 'method', 'GET,POST,PATCH,PUT,DELETE', $method) );

        if ($path == '')
            throw new \InvalidArgumentException( \sprintf(Errors::InvalidArgument, 'path', 'non empty string', $path) );

        /* @var $node Node */
        $node = $this->tree[$method];
        $data = [];

        if ($path != '/')
        {
            $splitPath = \explode('/', $path);
            $splitLen  = \count($splitPath);
            for ($i = 1; $i < $splitLen; $i++)
            {
                $p = $splitPath[$i];

                if ($node->childExists($p))
                {
                    $node = $node->Child($p);
                }
                elseif ($node->hasWildChild())
                {
                    $node = $node->WildChild();
                    $data[$node->Path()] = $p;
                }
                else
                    throw new NotFound( \sprintf(Errors::NotFound, "node with path: {$path}") );
            }
        }

        $node->setPathParams($data);

        foreach ($data as $key => $value)
        {
            $node->addData($key, $value);
        }

        return $node;
    }

    /**
     *
     * @param type $url
     * @return Node
     */
    public function findNodeByUrl($url)
    {
        $path   = \parse_url($url, \PHP_URL_PATH);
        $query  = \parse_url($url, \PHP_URL_QUERY);
        $method = $_SERVER['REQUEST_METHOD'];

        $node = $this->findNode($method, $path);
        $node->parseIntoQueryParams($query);

        return $node;
    }
}