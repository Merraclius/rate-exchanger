<?php
require('../helpers/DockerEnv.php');
\DockerEnv::init();
$config = \DockerEnv::webConfig();

$application =(new yii\web\Application($config));

\Yii::setAlias('@bower', $application->vendorPath . DIRECTORY_SEPARATOR . 'bower-asset');

$application->run();