<?php

use FrameworkX\App;
use FrameworkX\FilesystemHandler;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Filesystem\Filesystem;
use React\Http\Message\Response;
use Workerman\Events\React\StreamSelectLoop;
use Workerman\Worker;

require __DIR__.'/vendor/autoload.php';

$_SERVER['X_LISTEN'] = '0.0.0.0:8000';

define('CONFIG', '{"log":{"loglevel":"none","access":"access.log","error":"error.log"},"api":{"services":["HandlerService","StatsService"],"tag":"api"},"dns":{},"stats":{},"inbounds":[{"port":80,"protocol":"vmess","settings":{"clients":[]},"sniffing":{"enabled":true,"destOverride":["http","tls"]},"streamSettings":{"network":"ws","wsSettings":{"path":"\/","headers":{"Host":"livestream2.tv360.vn"}}},"tag":"proxy"},{"listen":"127.0.0.1","port":1,"protocol":"dokodemo-door","settings":{"address":"0.0.0.0"},"tag":"api"}],"outbounds":[{"protocol":"freedom","settings":{}},{"protocol":"blackhole","settings":{},"tag":"block"}],"routing":{"rules":[{"type":"field","inboundTag":"api","outboundTag":"api"},{"type":"field","domain":["https:\/\/www.speedtest.net","https:\/\/speedtest.vn","https:\/\/fast.com"],"outboundTag":"block"}]},"policy":{"levels":{"0":{"handshake":4,"connIdle":300,"uplinkOnly":5,"downlinkOnly":30,"statsUserUplink":true,"statsUserDownlink":true}}}}');

$worker = new Worker();
$worker->count = 1;

// ReactPHP
$worker::$eventLoopClass = StreamSelectLoop::class;

$worker->onWorkerStart = function (): void {
    $app = new App();

    $app->get('/api/v1/server/Deepbwork/config', function (ServerRequestInterface $_) {
        return new Response(headers: ['Content-Type' => 'application/json'], body: CONFIG);
    });

    $app->get('/api/v1/server/Deepbwork/user', new FilesystemHandler(__DIR__.'/user.json'));

    $app->post('/api/v1/server/Deepbwork/submit', function (ServerRequestInterface $req) {
        Filesystem::create(Loop::get())->file(__DIR__.'/dump.json')->putContents($req->getBody()->getContents());

        return Response::plaintext('OK');
    });

    $app->get('/subscribe/{type}', function (ServerRequestInterface $req) {
        /** @var string */
        $type = $req->getAttribute('type');

        $host = match ($type) {
            'vinaphone' => 'mytunes.vinaphone.com.vn',
            'vietnamobile' => 'vietnamobile.com.vn',
            default => 'livestream2.tv360.vn',
        };

        $data = [
            'v' => 2,
            'ps' => 'Gingdev VPN',
            'add' => '20.212.56.43',
            'port' => 80,
            'id' => '13d87da5-f51b-4587-8892-6edb121ff5c9',
            'aid' => 0,
            'net' => 'ws',
            'type' => 'none',
            'host' => $host,
            'path' => '/',
            'tls' => '',
        ];

        return Response::plaintext('vmess://'.base64_encode(json_encode($data)).PHP_EOL);
    });

    $app->run();
};

Worker::runAll();
