<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@root', dirname(dirname(__DIR__)));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@upload', dirname(dirname(__DIR__)) . '/frontend/web/upload');
Yii::setAlias('@resize', dirname(dirname(__DIR__)) . '/frontend/web/upload/resize');
Yii::setAlias('@documents', dirname(dirname(__DIR__)) . '/frontend/web/upload/documents');
Yii::setAlias('@images', dirname(dirname(__DIR__)) . '/frontend/web/images');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
