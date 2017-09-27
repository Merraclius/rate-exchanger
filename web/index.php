<?php
require('../helpers/DockerEnv.php');
\DockerEnv::init();
$config = \DockerEnv::webConfig();
(new yii\web\Application($config))->run();
