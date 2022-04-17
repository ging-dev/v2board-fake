<?php

declare(strict_types=1);

namespace Gingdev\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class HostMiddleware
{
    public function __invoke(ServerRequestInterface $req, callable $next)
    {
        $host = match ($req->getAttribute('type')) {
            'vinaphone'    => 'mytunes.vinaphone.com.vn',
            'vietnamobile' => 'vietnamobile.com.vn',
            default        => 'livestream2.tv360.vn',
        };

        $req = $req->withAttribute('host', $host);

        return $next($req);
    }
}
