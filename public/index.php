<?php

/******************************* LOADING & INITIALIZING BASE APPLICATION ****************************************/

// Configuration for error reporting, useful to show every little problem during development
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Load Composer's PSR-4 autoloader (necessary to load Slim, Mini etc.)
require '../vendor/autoload.php';

$app = new \Slim\Slim();

$app->view = new \Slim\Views\Twig();
$app->view->setTemplatesDirectory("../Mini/view");

/* CONFIGS 
*******************************************************/

require '../Mini/etc/db_config.php';

// Configs for mode "development" (Slim's default), see the GitHub readme for details on setting the environment
$app->configureMode('development', function () use ($app) {

	// pre-application hook, performs stuff before real action happens @see http://docs.slimframework.com/#Hooks
	$app->hook('slim.before', function () use ($app) {

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

	$app->config([
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

$db = \Mini\lib\DB::createDB($app->config('database'));

/* THE MODEL 
*******************************************************/
$model = new \Mini\Model\Model($db);
$user = new \Mini\lib\User($db);


/* THE ROUTES / CONTROLLERS 
*******************************************************/
// Index
$app->get('/', function () use ($app, $model) {
	$testing = ['asdf' => "hello"];
	$app->render('base/index.twig', ['test' => $testing]);
});

$app->post('/login', function () use ($app, $model) {
	$app->render('not_secure.twig', ["login" => $_POST['login'] ]);
});

// About
$app->group('/about', function () use ($app, $model) {
	$app->get('/', function () use ($app, $model) {
		$app->render('base/about.twig');
	});

});

// Admin
$app->group('/admin', function () use ($app, $model) {

	$app->get('/', function () use ($app, $model) {
		$input = ['releases' => $model->getAllReleases()];
		
		$app->render('admin/admin.index.twig', $input);
	});

	$app->group('/release', function () use ($app, $model) {

		$app->get('/', function () use ($app, $model) {
			$app->render('admin/release.twig');
		});

		$app->post('/add', function () use ($app, $model) {
			$model->addRelease($_POST["ext_release"]);
			$app->redirect('/admin');
		});

	});
});

/* Run
*******************************************************/
$app->run();
