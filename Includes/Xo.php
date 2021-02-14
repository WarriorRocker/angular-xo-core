<?php

/**
 * Xo for Angular base class.
 *
 * @since 1.0.0
 *
 * @author Travis Brown <warriorrocker@gmail.com>
 */
class Xo {
	/**
	 * @var string
	 */
	public $name = 'Xo for Angular';

	/**
	 * @var string
	 */
	public $plugin = 'xo-for-angular/xo-angular.php';

	/**
	 * @var array
	 */
	public $paths = array('/');

	/**
	 * @var string
	 */
	public $baseDir;

	/**
	 * @var XoFilters
	 */
	public $Filters;

	/**
	 * @var XoServices
	 */
	public $Services;

	/**
	 * @var XoOptions
	 */
	public $Options;

	/**
	 * @var XoApi
	 */
	public $Api;

	/**
	 * @param string $basedir
	 */
	function __construct($basedir) {
		$this->baseDir = $basedir;

		$this->Includes();
		$this->Init();
	}

	function Init() {
		$this->Services = new XoServices($this);
		$this->Filters = new XoFilters($this);
		$this->Options = new XoOptions($this);
		$this->Api = new XoApi($this);
	}

	function Includes() {
		$this->RequireOnce('Includes/Services/Services.class.php');
		$this->RequireOnce('Includes/Filters/Filters.class.php');
		$this->RequireOnce('Includes/Options/Options.class.php');
		$this->RequireOnce('Includes/Api/Api.class.php');
	}

	/**
	 * @param string|array $files
	 */
	function RequireOnce($files, $require = true) {
		if (is_array($files)) {
			foreach ($files as $file)
				$this->RequireOnce($file, $require);
		} else {
			foreach ($this->paths as $path) {
				$file = $this->baseDir . $path . '/' . $files;

				if (!file_exists($file))
					continue;

				if ($require)
					require_once($file);
				else
					include($file);
			}
		}
	}

	function GetFile($file, $relative = false) {
		$index = count($this->paths);

		while ($index) {
			$include = $this->baseDir .$this->paths[--$index] . '/' . $file;
			if (!file_exists($include))
				continue;

			if ($relative)
				return '/' . ltrim(str_replace('\\', '/', substr($include, strlen(ABSPATH))), '/\\');
			else
				return $include;
		}

		return false;
	}
}
