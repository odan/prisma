<?php

require_once __DIR__ . '/../config/bootstrap.php';

call_user_func(function () {
    // Set the real base path
    $request = http()->withBasePath();
    container()->share('request', $request);

    $response = router()->dispatch($request, response());
    emitter()->emit($response);
});
