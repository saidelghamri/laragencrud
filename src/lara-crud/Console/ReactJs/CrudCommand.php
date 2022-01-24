<?php

namespace LaraCrud\Console\ReactJs;

use Illuminate\Console\Command;
use LaraCrud\Crud\ReactJs\ReactJsApiEndpointCrud;
use LaraCrud\Crud\ReactJs\ReactJsFormCrud;
use LaraCrud\Crud\ReactJs\ReactJsModelCrud;
use LaraCrud\Crud\ReactJs\ReactJsServiceCrud;
use LaraCrud\Helpers\Helper;

class CrudCommand extends Command
{
    use Helper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reactjs:crud {model} {controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ReactJs Full CRUD Generator based on Model and Controller';

    public function handle()
    {
        try {
            $model = $this->getModal($this->argument('model'));
            $controller = $this->getController($this->argument('controller'));

            $this->warn('Creating Model....');
            $modelCrud = new ReactJsModelCrud($model);
            $modelCrud->save();
            $this->info('Model Created successfully');

            $this->warn('Creating Api Endpoint....');
            $controllerCrud = new ReactJsApiEndpointCrud(get_class($controller));
            $controllerCrud->save();
            $this->info('Api endpoint successfully');

            $this->warn('Creating API Service....');
            $serviceCrud = new ReactJsServiceCrud(get_class($controller));
            $serviceCrud->save();
            $this->info('API Service created successfully');

            $this->warn('Creating Form....');
            $formCrud = new ReactJsFormCrud($model, $controller);
            $formCrud->save();
            $this->info('Form created successfully');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     *  Check if Model or Parent Model exists . If so then create object from them otherwise return warning and exit.
     *
     * @param mixed $model
     */
    private function getModal($model)
    {
        if (class_exists($model)) {
            return new $model();
        }

        $namespace = $this->getFullNS(config('laracrud.model.namespace', 'App'));
        $model = rtrim($namespace, '\\') . '\\' . $model;

        return new $model();
    }

    /**
     * @param mixed $controller
     *
     * @throws \ReflectionException
     */
    protected function getController(string $controller)
    {
        if (! class_exists($controller)) {
            $namespace = config('laracrud.controller.apiNamespace');
            $namespace = $this->getFullNS($namespace);
            $controller = rtrim($namespace, '\\') . '\\' . $controller;
        }

        return app()->make($controller);
    }
}
