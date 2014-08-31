<?php

use Bolt\Extension\Bolt\NginxCacheHelper\Extension;

$app['extensions']->register(new Extension($app));
