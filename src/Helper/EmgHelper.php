<?php

namespace Krlove\EloquentModelGenerator\Helper;

use Illuminate\Support\Str;
use Krlove\EloquentModelGenerator\Config;

/**
 * Class EmgHelper
 * @package Krlove\EloquentModelGenerator\Helper
 */
class EmgHelper
{
	/**
	 * @var string
	 */
	const DEFAULT_PRIMARY_KEY = 'id';

	/**
	 * @param string $fullClassName
	 * @return string
	 */
	public function getShortClassName($fullClassName)
	{
		$pieces = explode('\\', $fullClassName);

		return end($pieces);
	}

	/**
	 * @param string $className
	 * @param bool $plural
	 * @return string
	 */
	public function getDefaultTableName($className, $plural = true)
	{
		$className = Str::snake($className);
		if($plural) {
			$className = Str::plural($className);
		}
		return $className;
	}

	/**
	 * @param string $table
	 * @param bool $singolar
	 * @return string
	 */
	public function getDefaultForeignColumnName($table, $singolar = true)
	{
		if($singolar) {
			$table = Str::singular($table);
		}
		return sprintf('%s_%s', $table, self::DEFAULT_PRIMARY_KEY);
	}

	/**
	 * @param string $tableOne
	 * @param string $tableTwo
	 * @param bool $singolar
	 * @return string
	 */
	public function getDefaultJoinTableName($tableOne, $tableTwo, $singolar = true)
	{
		if($singolar) {
			$tableOne = Str::singular($tableOne);
			$tableTwo = Str::singular($tableTwo);
		}
		$tables = [$tableOne, $tableTwo];
		sort($tables);

		return sprintf(implode('_', $tables));
	}
}
