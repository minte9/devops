<?php

$versions = [];

$apache = explode(" ", explode("/", $_SERVER['SERVER_SOFTWARE'])[1])[0];
array_push($versions, $apache);

$php = phpversion();
array_push($versions, $php);

echo join(" / ", $versions);
