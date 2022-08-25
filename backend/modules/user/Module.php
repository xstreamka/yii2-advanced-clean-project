<?php

namespace backend\modules\user;

/**
 * user module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @var string the default route of this module. Defaults to 'default'
     */
    public $defaultRoute = 'user';

    /**
     * @var string the namespace that controller classes are in
     */
    public $controllerNamespace = 'backend\modules\user\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
