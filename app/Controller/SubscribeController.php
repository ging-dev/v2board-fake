<?php

declare(strict_types=1);

namespace Gingdev\Controller;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class SubscribeController
{
    public function __invoke(ServerRequestInterface $req): Response
    {
        $data = [
            'v'    => 2,
            'ps'   => 'Gingdev VPN',
            'add'  => '20.212.56.43',
            'port' => 80,
            'id'   => '13d87da5-f51b-4587-8892-6edb121ff5c9',
            'aid'  => 0,
            'net'  => 'ws',
            'type' => 'none',
            'host' => $req->getAttribute('host'),
            'path' => '/',
            'tls'  => '',
        ];

        return Response::plaintext(base64_encode('vmess://'.base64_encode(json_encode($data)).PHP_EOL));
    }
}
