<?php

namespace Krlove\EloquentModelGenerator\Command;

use Illuminate\Config\Repository as AppConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Generator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class GenerateDBCommand
 * @package Krlove\EloquentModelGenerator\Command
 */
class GenerateDBCommand extends Command
{
	/**
	 * @var string
	 */
	protected $name = 'krlove:generate:db';

	/**
	 * @var Generator
	 */
	protected $generator;

	/**
	 * @var AppConfig
	 */
	protected $appConfig;

	/**
	 * GenerateDBCommand constructor.
	 * @param Generator $generator
	 * @param AppConfig $appConfig
	 */
	public function __construct(Generator $generator, AppConfig $appConfig)
	{
		parent::__construct();

		$this->generator = $generator;
		$this->appConfig = $appConfig;
	}

	/**
	 * Executes the command
	 */
	public function fire()
	{
		$queryTabelleDB = json_decode(json_encode(DB::select("SHOW TABLES")), true);
		$tabelleDB = [];
		foreach ($queryTabelleDB as $t) {
			list($chiave) = array_keys($t);
			$tabelleDB[] = $t[$chiave];
		}
		$this->output->writeln("Lista delle tabelle: " . implode($tabelleDB, ", \n                     "));


		foreach ($tabelleDB as $t) {
			$nameModel = camel_case($t);
			$nameTable = $t;

			$config = $this->createConfig($nameModel, $nameTable);

			$model = $this->generator->generateModel($config);

			$this->output->writeln(sprintf('Model %s generated', $model->getName()->getName()));
		}

		$this->output->writeln('All model generated');
	}

	/**
	 * Add support for Laravel 5.5
	 */
	public function handle()
	{
		$this->fire();
	}

	/**
	 * @return Config
	 */
	protected function createConfig($nameModel, $nameTable)
	{
		$config = [];

		$config['class-name'] = $nameModel;
		$config['table-name'] = $nameTable;

		$config['db_types'] = $this->appConfig->get('eloquent_model_generator.db_types');

		return new Config($config, $this->appConfig->get('eloquent_model_generator.model_defaults'));
	}

	/**
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['class-name', InputArgument::OPTIONAL, 'Model class name'],
		];
	}

	/**
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['table-name', 'tn', InputOption::VALUE_OPTIONAL, 'Name of the table to use', null],
			['output-path', 'op', InputOption::VALUE_OPTIONAL, 'Directory to store generated model', null],
			['namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Namespace of the model', null],
			['base-class-name', 'bc', InputOption::VALUE_OPTIONAL, 'Model parent class', null],
			['no-timestamps', 'ts', InputOption::VALUE_NONE, 'Set timestamps property to false', null],
			['date-format', 'df', InputOption::VALUE_OPTIONAL, 'dateFormat property', null],
			['connection', 'cn', InputOption::VALUE_OPTIONAL, 'Connection property', null],
			['backup', 'b', InputOption::VALUE_NONE, 'Backup existing model', null]
		];
	}
}
