<?php
use CkTools\Lib\ApiReturnCode;

// This file is used when the AuthComponent reacts to a AJAX unauthenticated request
echo json_encode([
    'code' => ApiReturnCode::NOT_AUTHENTICATED
]);