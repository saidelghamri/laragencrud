<?php

namespace LaraCrud\Crud;

use Illuminate\Database\Eloquent\Model;
use LaraCrud\Contracts\Crud;
use LaraCrud\Helpers\Helper;
use LaraCrud\Helpers\TemplateManager;
use LaraCrud\Repositories\ControllerRepository;

class ModelRepository implements Crud
{
    use Helper;

    /**
     * Model Name.
     *
     * @var string
     */
    protected string $modelName;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var string
     */
    protected string $fileName = '';

    /**
     * Sub Path of the Controller.
     * Generally Controller are stored in Controllers folder. But for grouping Controller may be put into folders.
     *
     * @var string
     */
    public string $path = '';

    /**
     * @var string
     */
    public string $namespace;

    /**
     * Namespace version of subpath.
     *
     * @var string
     */
    protected string $subNameSpace = '';

    /**
     * @var bool|string
     */
    protected $parentModel;

    /**
     * @var \LaraCrud\Repositories\ControllerRepository
     */
    protected ControllerRepository $controllerRepository;

    /**
     * ControllerCrud constructor.
     *
     * @param \LaraCrud\Repositories\ControllerRepository $controllerRepository
     * @param \Illuminate\Database\Eloquent\Model         $model
     * @param bool                                        $api
     *
     * @internal param array $except
     */
    public function __construct(
        ControllerRepository $controllerRepository,
        Model $model
    ) {
        $this->model = $model;
        $this->generateModelNAme();
//        $ns = ! empty($api) ? config('laracrud.controller.apiNamespace') : config('laracrud.controller.namespace');
        $ns = config('laracrud.repository.namespace');
        $this->namespace = trim($this->getFullNS($ns), '/') . $this->subNameSpace;
        $this->controllerRepository = $controllerRepository;
    }
    public function generateModelNAme(){
        return $this->modelName = (new \ReflectionClass($this->model))->getShortName();
    }
    /**
     * Generate full code and return as string.
     *
     * @return string
     */
    public function template(): string
    {
//        $modelShortName = (new \ReflectionClass($this->model))->getShortName();
        $modelShortName = $this->modelName;
        $this->controllerRepository->build();
        $tempMan = new TemplateManager('Repository/template.txt', [
            'namespace' => $this->namespace,
            'fullmodelName' => get_class($this->model),
            'controllerName' => $this->fileName,
            'methods' => implode("\n", $this->controllerRepository->getCode()),
            'importNameSpace' => $this->makeNamespaceImportString(),
            'modelVariable' => lcfirst($this->modelName),
            'model' => $this->modelName,
            'repository'=> $this->modelName .'Repository',
            'repositoryVariable'=> lcfirst($this->modelName) .'Repository',
        ]);

        return $tempMan->get();
    }

    /**
     * Get code and save to disk.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function save()
    {
        $this->checkPath('');
        $fileName = ! empty($this->fileName) ? $this->getFileName($this->fileName) . 'GGG.php' : $this->modelName . 'Repository' . '.php';
        $filePath = base_path($this->toPath($this->namespace)) . '/' . $fileName;
        if (file_exists($filePath)) {
            throw new \Exception($filePath . ' already exists');
        }
        $controller = new \SplFileObject($filePath, 'w+');
        $controller->fwrite($this->template());
    }

    /**
     * Get full newly created fully qualified Class namespace.
     */
    public function getFullName()
    {
        $fileName = ! empty($this->fileName) ? $this->getFileName($this->fileName) : $this->controllerName . 'Controller';

        return $this->namespace . '\\' . $fileName;
    }

    public function makeNamespaceImportString()
    {
        $ns = '';
        foreach ($this->controllerRepository->getImportableNamespaces() as $namespace) {
            $ns .= "\n use " . $namespace . ';';
        }

        return $ns;
    }

}
