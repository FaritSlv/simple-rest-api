<?php

namespace App\Service\Api;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Api {
    /** @var Request */
    protected $request = null;

    /** @var Response */
    protected $response = null;

    /**
     * Api constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request  = $request;
        $this->response = new JsonResponse();

        $this->response->headers->set("Access-Control-Allow-Orgin", "*");
        $this->response->headers->set("Access-Control-Allow-Methods", "*");
        $this->response->headers->set("Content-Type", "application/json");
    }

    /**
     * @param Request $request
     * @return self
     * @throws Exception
     * @throws ReflectionException
     */
    public static function run(Request $request) {
        $action    = self::getAction($request->getMethod());
        $params    = self::getParams($request->getRequestUri());
        $className = self::getClassName($params);
        if(strlen($className) === 0){
            throw new RuntimeException('Class name is required', Response::HTTP_BAD_REQUEST);
        }

        $class     = __NAMESPACE__ . "\\{$className}";
        /** @var Api|ReflectionClass $class */
        $reflector = new ReflectionClass($class);
        $reflector->newInstanceArgs([$request]);

        if ($reflector->hasMethod($action) && ($method = $reflector->getMethod($action))) {
            $resultClass = new $class($request);
            unset($params[0]);
            reset($params);
            $method->invokeArgs($resultClass, [$params]);
            return $resultClass;
        } else {
            throw new RuntimeException('Invalid Method', Response::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @param $data
     * @param int $status
     * @param bool $isSuccess
     * @return Api
     */
    public function setResponse($data = null, $status = null, $isSuccess = true) {
        $statusResult = $status !== null ? $status : Response::HTTP_INTERNAL_SERVER_ERROR;
        $this->response->setStatusCode($statusResult);
        $result = [
            "success" => $isSuccess,
            "result"  => $data,
        ];
        $this->response->setData($result);
        return $this;
    }

    /**
     * @param $method
     * @return string
     * @throws Exception
     */
    protected static function getAction($method) {
        switch ($method) {
            case Request::METHOD_GET:
                return 'view';
            case Request::METHOD_POST:
                return 'create';
            case Request::METHOD_PATCH:
            case Request::METHOD_PUT:
                return 'update';
            case Request::METHOD_DELETE:
                return 'delete';
            default:
                throw new Exception("Unknown method");
        }
    }

    /**
     * @param array $params
     * @return string
     * @throws Exception
     */
    protected static function getClassName(array $params = []) {
        if (count($params) === 0) {
            throw new RuntimeException("You must pass the class name");
        }
        return ucfirst($params[0]);
    }

    /**
     * @param $requestUri
     * @return string
     * @throws Exception
     */
    protected static function getParams($requestUri) {
        $uri     = str_replace(API_ROOT, "", $requestUri);
        $trimUri = trim($uri, "/");
        $params  = explode("/", $trimUri);
        return $params;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getJsonParameters() {
        $data   = $this->request->getContent();
        $result = strlen($data) > 0 ? json_decode($data, true) : [];
        return $result;
    }

    abstract public function view();

    abstract public function create();

    abstract public function update(array $params = []);

    abstract public function delete(array $params = []);
}