<?php

include 'vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$writer = new \React\Stream\WritableResourceStream(STDOUT, $loop);
$browser = new \React\Http\Browser($loop);

$server = new \React\Http\Server($loop,
    function (\Psr\Http\Message\ServerRequestInterface $request) use ($writer, $browser) {
        $writer->write(sprintf(
            "New request %s %s".PHP_EOL,
            $request->getMethod(),
            $request->getUri()
        ));

        $promise = $browser->request($request->getMethod(), $request->getUri(), $request->getHeaders(),
            $request->getBody());
        return $promise->then(function (\Psr\Http\Message\ResponseInterface $response) {
            return $response;
        }, function (Exception $exception) {
            var_dump("Error ".$exception->getMessage());
        });
    });

$socket = new React\Socket\Server('0.0.0.0:8888', $loop);

$server->listen($socket);

$loop->run();