<?php

require_once __DIR__ . '/../config/bootstrap.php';

call_user_func(function () {
    $request = request();
    $response = response();

    // Set the real base path
    $http = new \App\Util\Http($request, $response);
    $request = $http->withBasePath();
    container()->share('request', $request);

    $response = router()->dispatch($request, $response);
    emitter()->emit($response);
});
