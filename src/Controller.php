<?php

namespace LumenBaseCRUD;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as LumenController;

/**
 * Classe base para CRUDS com Lumen
 *
 * @author Guilherme Alves <guihalves20@gmail.com>
 */
class Controller extends LumenController
{
    use APIResponse;

    /**
     * String com o model
     * Deve ser definida como Model::class
     *
     * @var string
     */
    protected $model = '';

    /**
     * Regras de validação no verbo POST
     *
     * @var array
     */
    protected array $postRules = [];

    /**
     * Regras de validação no verbo PUT
     *
     * @var array
     */
    protected array $putRules = [];

    /**
     * Construtor básico
     * Define as regras de putRules como as mesmas de postRules, podendo ser 
     * sobrescritas
     */
    public function __construct()
    {
        $this->putRules = array_merge($this->postRules, $this->putRules);
    }

    /**
     * Retorna um objeto de acordo com seu ID
     *
     * @param integer $id
     * @return JsonResponse|void
     */
    public function show(int $id): JsonResponse
    {
        // Busca o objeto
        $data = $this->model::find($id);

        if (!$data) {
            return $this->response(404);
        }

        $this->preShow($data);
        return $this->response(200, compact(['data']));
    }

    /**
     * Retorna todos os objetos paginados
     *
     * @return JsonResponse|void
     */
    public function index(Request $request): JsonResponse
    {
        $pageSize = config('database.pageSize') ?? 10;
        $result   = $this->model::paginate($pageSize)->toArray();
        
        // Recupero os dados e a paginação
        $data       = $result['data'];
        $pagination = ($data) ? Arr::except($result, 'data') : null;

        $this->preIndex($data);
        return $this->response(200, compact(['data', 'pagination']));
    }

    /**
     * Salva um objeto
     *
     * @param Request $request
     * @return JsonResponse|void
     */
    public function store(Request $request): JsonResponse
    {
        // A partir das postRules, valida os dados
        $data = $request->all();
        $validator = Validator::make($data, $this->postRules);

        // Se a validação falhar
        if ($validator->fails()) {
            $fails = $validator->errors()->all();
            return $this->response(400, compact('fails'), 'Parâmetros inválidos');
        }

        $preStoreReturns = $this->preStore($data);
        if ($preStoreReturns) {
            return $preStoreReturns;
        }

        // Efetivamente cria o objeto
        $created = $this->model::create($data);

        // Se houver algum erro, já retorno
        if (!$created) {
            return $this->response(500);
        }

        // senão, chamo a função posStore e retorno uma resposta de API
        $this->posStore($created);
        return $this->response(201, ['id' => $created->id]);
    }

    /**
     * Atualiza um objeto
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|void
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // Busca o objeto
        $object = $this->model::find($id);

        // Se o objeto não for encontrado
        if (!$object) {
            return $this->response(404);
        }

        // Valido os dados
        $data = $request->all();
        $validator = Validator::make($data, $this->putRules);

        // Se a validação falhar
        if ($validator->fails()) {
            $fails = $validator->errors()->all();
            return $this->response(400, compact('fails'), 'Parâmetros inválidos');
        }

        $preUpdateReturns = $this->preUpdate($object);
        if ($preUpdateReturns) {
            return $preUpdateReturns;
        }

        $updated = $object->update($data);
        // Se houver algum erro, já retorno
        if (!$updated) {
            return $this->response(500);
        }

        // senão, chamo a posUpdate e retorno
        $this->posUpdate($object);
        return $this->response(200);
    }

    /**
     * Deleta um objeto
     *
     * @param int $id
     * @return JsonResponse|void
     */
    public function delete(int $id)
    {
        $object = $this->model::find($id);

        // Se o objeto não for encontrada
        if (!$object) {
            return $this->response(404);
        }

        $preDeleteReturns = $this->preDelete($object);
        if ($preDeleteReturns) {
            return $preDeleteReturns;
        }

        $object->delete();
        $this->posDelete($object);

        return $this->response(200);
    }

    /**
     * Executada antes de exibição de um objeto
     *
     * @param Model $data
     * @return void
     */
    protected function preShow(&$data) {}

    /**
     * Executada antes de exibição de uma lista de objetos
     *
     * @param array $data
     * @return void
     */
    protected function preIndex(array &$data) {}

    /**
     * Executada antes da criação de um objeto
     *
     * @param array $data dados do objeto
     * @return JsonResponse|void
     */
    protected function preStore(array &$data) {}

    /**
     * Executada após a criação de um objeto
     *
     * @param Model $object
     * @return void
     */
    protected function posStore(Model $object) {}

    /**
     * Executada antes da alteração de um objeto
     *
     * @param Model $object
     * @return JsonResponse|void
     */
    protected function preUpdate(Model &$object) {}

    /**
     * Executada após a alteração de um objeto
     *
     * @param Model $object
     * @return void
     */
    protected function posUpdate(Model $object) {}

    /**
     * Executada antes da deleção de um objeto
     *
     * @param Model $object
     * @return JsonResponse|void
     */
    protected function preDelete(Model &$object) {}

    /**
     * Executada após a deleção de um objeto
     *
     * @param Model $object
     * @return void
     */
    protected function posDelete(Model $object) {}

}
