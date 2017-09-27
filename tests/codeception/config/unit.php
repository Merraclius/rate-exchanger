<?php
/**
 * This is the configuration for the Yii app during unit tests
 */
return yii\helpers\ArrayHelper::merge(
    require(\DockerEnv::APP_DIR . 'config/web.php'),
    require(\DockerEnv::TEST_DIR . 'codeception/config/config.php')
);
