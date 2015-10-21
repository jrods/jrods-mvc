<?php

/* LOADING & INITIALIZING BASE APPLICATION 
*******************************************************/
// Configuration for error reporting, useful to show every little problem during development
error_reporting(E_ALL);
ini_set("display_errors", 1);

require '../vendor/autoload.php';

$slim = new \Slim\Slim();

$slim->view = new \Slim\Views\Twig();
$slim->view->setTemplatesDirectory("../jrods/view");

/* CONFIGS 
*******************************************************/
require '../jrods/etc/db_config.php';

$slim->configureMode('development', function () use ($slim) {

	$slim->hook('slim.before', function () use ($slim) {

		// SASS-to-CSS compiler @see https://github.com/panique/php-sass
		//SassCompiler::run("scss/", "css/");

		// CSS minifier @see https://github.com/matthiasmullie/minify
		//$minifier = new MatthiasMullie\Minify\CSS('css/style.css');
		//$minifier->minify('css/style.css');

		// JS minifier @see https://github.com/matthiasmullie/minify
		// DON'T overwrite your real .js files, always save into a different file
		//$minifier = new MatthiasMullie\Minify\JS('js/application.js');
		//$minifier->minify('js/application.minified.js');
	});

	$slim->config([
		'debug' => true,
		'database' => [
			'db_host' => DB_HOST,
			'db_port' => DB_PORT,
			'db_name' => DB_NAME,
			'db_user' => DB_USER,
			'db_pass' => DB_PASS
		]
	]);
});

/* THE MODEL 
*******************************************************/
$db = \jrods\lib\DB::createDB($slim->config('database'));

$model = new \jrods\Model\Model($db);
$user = new \jrods\lib\User($db);

/* THE ROUTES / CONTROLLERS 
*******************************************************/
// Index
$slim->get('/', function () use ($slim, $model) {
	$testing = ['asdf' => "hello"];
	$slim->render('base/index.twig', ['test' => $testing]);
});

$slim->post('/login', function () use ($slim, $model) {
	$slim->render('not_secure.twig', ["login" => $_POST['login'] ]);
});

// About
$slim->group('/about', function () use ($slim, $model) {
	$slim->get('/', function () use ($slim, $model) {
		$slim->render('base/about.twig');
	});

});

$slim->get('/phpinfo', function () {
	echo phpinfo();
});

// Admin
$slim->group('/admin', function () use ($slim, $model) {

	$slim->get('/', function () use ($slim, $model) {
		$input = ['releases' => $model->getAllReleases()];
		
		$slim->render('admin/admin.index.twig', $input);
	});

	$slim->group('/release', function () use ($slim, $model) {

		$slim->get('/', function () use ($slim, $model) {
			$slim->render('admin/release.twig');
		});

		$slim->post('/add', function () use ($slim, $model) {
			$model->addRelease($_POST["ext_release"]);
			$slim->redirect('/admin');
		});

	});
});

/* Run
*******************************************************/
$slim->run();
