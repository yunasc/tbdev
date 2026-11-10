<?php

// The PHP development server does not read the project's .htaccess files.
$route = rawurldecode(explode('?', $_SERVER['REQUEST_URI'], 2)[0]);
if (preg_match('~(?:^|/)\.|^/(?:include|migrations|tests|dev|admin/modules)(?:/|$)|\.(?:torrent|sql|local\.php)$~i', $route)) {
    http_response_code(404);
    return true;
}
return false;
