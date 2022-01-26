<?php

namespace LaraCrud\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use LaraCrud\Contracts\DatabaseContract;
use LaraCrud\Crud\Model as ModelCrud;

class Model extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omnevo:model
        {table : MySQl Table name}
        {name? : Custom Model Name. e.g. MyPost}
        {--on= : Config options of model from config/laracrud.php you want to switch on. For example --on=mutators will activate mutators for your model.}
        {--off= : Config options from config/laracrud.php you want to switch of}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Model class based on table';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $table = $this->argument('table');
            $modelName = $this->argument('name');
            $on = $this->option('on');
            $off = $this->option('off');

            //Overwrite existing Configuration file for this Model Instance
            if (!empty($on)) {
                $ons = explode(',', $on);
                foreach ($ons as $option) {
                    config(["laracrud.model.$option" => true]);
                }
            }

            if (!empty($off)) {
                $offs = explode(',', $off);
                foreach ($offs as $option) {
                    config(["laracrud.model.$option" => false]);
                }
            }

            $databaseRepository = app()->make(DatabaseContract::class);

            if (strripos($table, ',')) {
                $table = explode(',', $table);

                foreach ($table as $tb) {
                    $databaseRepository->tableExists($tb);
                    $modelCrud = new ModelCrud($tb);
                    $modelCrud->save();
                }
            } else {
                $databaseRepository->tableExists($table);
                $modelCrud = new ModelCrud($table, $modelName);
                $modelCrud->save();
            }

            $this->info('Model class successfully created');
        } catch (\Exception $ex) {
            $this->error(sprintf('%s on line %s  in %s. Please see log file more more details.', $ex->getMessage(), $ex->getLine(), $ex->getFile()));
            Log::error($ex->getTraceAsString());
        }
    }
}
