<?php


namespace App\Service\Api;

use App\ORM\DataManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class User extends Api {
    /** @var DataManager */
    private $dataManager = null;

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {
        parent::__construct($request);
        $this->dataManager = new DataManager("users");
    }

    /**
     * @api {get} /api/user/ Получить список пользователей
     * @apiGroup User
     * @apiName view
     *
     * @apiParamExample {Url} массив полей для выборки, можно создавать алиясы:
     * ?select[]=id&select[testName]=name
     *
     * @apiSuccess {Object[]} result результат выборки
     * @apiSuccess {Number} result.id id пользователя
     * @apiSuccess {String} result.name имя пользователя
     * @apiSuccess {String} result.email e-mail пользователя
     *
     * @apiSuccessExample {json} Пример ответа:
     * HTTP/1.1 200 OK
     * {
     *   "success": true,
     *   "result": [
     *     {
     *       "id": "1",
     *       "name": "Te1",
     *       "email": "trest@test.com"
     *     },
     *     {
     *       "id": "2",
     *       "name": "test2",
     *       "email": "test@test.ru"
     *     }
     *   ]
     * }
     *
     * @apiSampleRequest http://localhost:8000/api/user/?select[]=id&select[test]=name
     */
    /**
     * @return $this
     */
    public function view() {
        $select      = $this->request->get("select");
        $selectQuery = $select !== null && is_array($select) ? $select : [];
        $result      = $this->dataManager->getList($selectQuery);
        $this->setResponse($result, Response::HTTP_OK);
        return $this;
    }

    /**
     * @api {post} /api/user/ Создать пользователя
     * @apiGroup User
     * @apiName create
     *
     * @apiParamExample {json} Request-Example:
     * {
     *      "name": "testName",
     *      "email": "example@info.loc"
     * }
     *
     * @apiSuccess {Boolean} result результат создания
     *
     * @apiSuccessExample {json} Пример ответа:
     * HTTP/1.1 200 OK
     * {
     *   "success": true,
     *   "result": true
     * }
     */
    /**
     * @return $this
     * @throws Exception
     */
    public function create() {
        $params = $this->getJsonParameters();
        if (count($params) === 0) {
            throw new Exception("Params not found");
        }
        $result = $this->dataManager->create($params);
        $this->setResponse($result, Response::HTTP_OK);
        return $this;
    }

    /**
     * @api {put} /api/user/:id Обновить пользователя
     * @apiGroup User
     * @apiName update
     *
     * @apiParam {Number} :id идентификатор пользователя
     *
     * @apiParamExample {json} Request-Example:
     * {
     *      "name": "testName",
     *      "email": "example@info.loc"
     * }
     *
     * @apiSuccess {Boolean} result результат создания
     *
     * @apiSuccessExample {json} Пример ответа:
     * HTTP/1.1 200 OK
     * {
     *   "success": true,
     *   "result": true
     * }
     */
    /**
     * @param array $params
     * @return User
     * @throws Exception
     * @throws \App\ORM\Exception
     */
    public function update(array $params = []) {
        $paramsJSON = $this->getJsonParameters();
        if (count($paramsJSON) === 0) {
            throw new Exception("Params not found");
        }
        $primary = count($params) > 0 ? (int)current($params) : 0;

        if ($primary == 0) {
            throw new Exception("Primary is required");
        }
        $result = $this->dataManager->update($primary, $paramsJSON);
        $this->setResponse($result, Response::HTTP_OK);
        return $this;
    }

    /**
     * @api {delete} /api/user/:id Удалить пользователя
     * @apiGroup User
     * @apiName delete
     *
     * @apiParam {Number} :id идентификатор пользователя
     *
     * @apiSuccess {Boolean} result результат создания
     *
     * @apiSuccessExample {json} Пример ответа:
     * HTTP/1.1 200 OK
     * {
     *   "success": true,
     *   "result": true
     * }
     */
    /**
     * @param array $params
     * @return User
     * @throws Exception
     * @throws \App\ORM\Exception
     */
    public function delete(array $params = []) {
        $primary = count($params) > 0 ? (int)current($params) : 0;
        if ($primary == 0) {
            throw new Exception("Primary is required");
        }
        $result = $this->dataManager->delete($primary);
        $this->setResponse($result, Response::HTTP_OK);
        return $this;
    }
}