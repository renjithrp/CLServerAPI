<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

use Respect\Validation\Validator as v;

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../config/settings.php';
$app = new \Slim\App($settings);



$container = $app->getContainer();



v::with('Apps\\Validation\\Rules\\');



// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
#require __DIR__ . '/../src/middleware.php';

// Register routes
$container = new \Slim\Container();

require __DIR__ . '/../routes/UserRoutes.php';
require __DIR__ . '/../routes/ProfileRoutes.php';
require __DIR__ . '/../routes/RoleRoutes.php';
require __DIR__ . '/../routes/RatingRoutes.php';
require __DIR__ . '/../routes/TestimonialsRoutes.php';
require __DIR__ . '/../routes/SectionRoutes.php';
require __DIR__ . '/../routes/SubjectRoutes.php';
require __DIR__ . '/../routes/VerificationRoutes.php';
require __DIR__ . '/../routes/ExamsRoutes.php';
require __DIR__ . '/../routes/NotesRoutes.php';
require __DIR__ . '/../routes/DpRoutes.php';
require __DIR__ . '/../routes/SearchRoutes.php';


require __DIR__ . '/../routes/Routes.php';


// Run app
$app->run();
