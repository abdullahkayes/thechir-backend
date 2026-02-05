<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;

$controller = new ProductController();
$resp = $controller->new_products();
if (is_object($resp)) {
    echo "RESP_CLASS=" . get_class($resp) . "\n";
    if (method_exists($resp, 'getContent')) {
        echo $resp->getContent() . "\n";
    } elseif (method_exists($resp, 'original')) {
        var_export($resp->original());
    } else {
        var_export($resp);
    }
} else {
    var_export($resp);
}
