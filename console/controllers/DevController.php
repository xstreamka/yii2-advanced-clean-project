<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 11.02.2021
 * Time: 14:19
 */

namespace console\controllers;


use common\helpers\FileHelper;
use Yii;
use yii\console\Controller;

class DevController extends Controller
{

	/**
	 * НЕ ИСПОЛЬЗОВАТЬ.
	 *
	 * Очистка проекта перед коммитом, для репозитория GitHub.
	 * Очищает кеш, дебаг, логи и почту. А так же composer.lock и откатывает изменения при init.
	 */
	public function actionClean()
	{
		// Главные папки.
		$dirs = [
			'backend',
			'console',
			'frontend',
		];
		// Подпапки.
		$subDirs = [
			'cache',
			'debug',
			'logs',
			'mail',
		];

		foreach ($dirs as $dir) {
			foreach ($subDirs as $subDir) {
				$path = Yii::getAlias("@{$dir}/runtime/{$subDir}");
				FileHelper::removeDirectory($path);
			}
		}

		// Удаляем composer.lock, он не нужен.
		FileHelper::unlink(Yii::getAlias('@root/composer.lock'));
		// Чистим assets.
		$this->actionCleanAssets();
		// Откат изменений.
		$this->actionInit(true);
	}

	/**
	 * Очищает assets.
	 */
	public function actionCleanAssets()
	{
		$dirs = [
			'backend',
			'frontend',
		];

		foreach ($dirs as $dir) {
			$path = Yii::getAlias("@{$dir}/web/assets/*");
			foreach (glob($path) as $link) {
				FileHelper::removeDirectory($link);
			}
		}
	}

	/**
	 * Инициализация проекта во время установки.
	 * Переименует файлы .gitignore в свое первоначальное состояние.
	 * @param bool $back Вернуть файлы назад как было.
	 */
	public function actionInit(bool $back = false)
	{
		$dirs = [
			'backend',
			'common',
			'console',
			'frontend',
		];

		if (!$back) {
			$filenameOld = '.gitignore_';
			$filename = '.gitignore';
		} else {
			$filenameOld = '.gitignore';
			$filename = '.gitignore_';
		}

		foreach ($dirs as $dir) {
			$fileOld = Yii::getAlias("@{$dir}/config/{$filenameOld}");
			$file = Yii::getAlias("@{$dir}/config/{$filename}");
			if (file_exists($fileOld)) {
				rename($fileOld, $file);
			}
		}

		// А так же главный файл .gitignore в корне.
		$fileOld = Yii::getAlias("@root/{$filenameOld}");
		$file = Yii::getAlias("@root/{$filename}");

		if (!$back && file_exists($fileOld) && file_exists($file)) {
			FileHelper::unlink($file);
		}
		if (file_exists($fileOld) && !file_exists($file)) {
			rename($fileOld, $file);
		}
		if ($back && !file_exists($fileOld)) {
			$files = ['.idea', 'vendor'];
			file_put_contents($fileOld, implode(PHP_EOL, $files));
		}

		// Информация.
		if (!$back) {
			$files = [
				' > common/config/main-local.php',
				' > common/config/params-local.php',
			];

			echo '>>> Please change data in files:' . PHP_EOL . implode(PHP_EOL, $files);
		} else {
			echo '>>> Done!';
		}
	}
}