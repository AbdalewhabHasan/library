<?php
require __DIR__ . "/../vendor/autoload.php";
$config = require __DIR__ . "/../config/database.php";
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection($config['connections'][$config['default']]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$rows = \App\Models\AudioBook::latest()->take(10)->get(['id','title','cover_image_path','file_path','pdf_path','status']);
echo json_encode($rows->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
