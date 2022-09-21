<?php

use FrameworkX\App;
use FrameworkX\FilesystemHandler;
use Gingdev\Controller\SubscribeController;
use Gingdev\Middleware\HostMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Filesystem\Filesystem;
use React\Http\Message\Response;
use Workerman\Events\React\StreamSelectLoop;
use Workerman\Worker;

require __DIR__.'/vendor/autoload.php';

$worker = new Worker();

// Setting
$worker->count = 1;

// ReactPHP
$worker::$eventLoopClass = StreamSelectLoop::class;

$worker->onWorkerStart = function (): void {
    $app = new App();

    $app->get('/api/v1/server/Deepbwork/config', new FilesystemHandler(__DIR__.'/config.json'));

    $app->get('/api/v1/server/Deepbwork/user', new FilesystemHandler(__DIR__.'/user.json'));

    $app->post('/api/v1/server/Deepbwork/submit', function (ServerRequestInterface $req) {
        Filesystem::create(Loop::get())->file(__DIR__.'/dump.json')->putContents($req->getBody()->getContents());

        return Response::plaintext('OK');
    });

    $app->get('/subscribe/{type}', HostMiddleware::class, SubscribeController::class);

    $app->run();
};

Worker::runAll();
