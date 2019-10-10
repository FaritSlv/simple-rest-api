<?php
error_reporting(E_ALL);

use App\Service\Api\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . "/../../vendor/autoload.php";

$request    = Request::createFromGlobals();
$apiRoot    = str_replace($request->server->get("DOCUMENT_ROOT"), "", __DIR__);
$apiRootNix = str_replace("\\", "/", $apiRoot);
define("API_ROOT", $apiRootNix);
$dbPath = realpath($request->server->get("DOCUMENT_ROOT") . "/../data/apitest.db");
define("DB_PATH", $dbPath);

try {
    /** @var Api $api */
    $api = Api::run($request);
    $api->getResponse()->send();
} catch (Exception $exception) {
    $response = new JsonResponse(
        [
            "message" => $exception->getMessage(),
            "trace"   => $exception->getTraceAsString(),
        ],
        $exception->getCode()
    );
    $response->send();
}
