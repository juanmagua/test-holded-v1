<?php

use \Firebase\JWT\JWT;
use \Tuupola\Base62;


function generateJWT($uid){

    $base62 = new Tuupola\Base62;

    $now = new DateTime();
    $future = new DateTime("now +2 hours");
    $jti = $base62->encode(random_bytes(16));

    $secret = getenv("JWT_SECRET") ? getenv("JWT_SECRET") : $_ENV['JWT_SECRET'];

    $payload = [
        "jti" => $jti,
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "uid" => $uid
    ];

    return JWT::encode($payload, $secret, "HS256");
}
