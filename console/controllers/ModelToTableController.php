<?php

namespace console\controllers;


use c006\core\assets\CoreHelper;
use Yii;
use yii\db\Exception;

class ModelToTableController extends \yii\console\Controller
{


    public function actionRun()
    {

        $array = [];
        $path = Yii::getAlias('@console');
        $path .= '/runtime/models';

        $dirs = CoreHelper::recursiveDirectory($path, $path);

        foreach ($dirs as $dir) {

            $dir = $dir['item'];

            $array[] = self::readFile($dir['path'] . '/' . $dir['file']);
        }

        foreach ($array as $table) {

            self::createTable($table);
        }
    }

    /**
     * @param $array
     * @throws Exception
     */
    private function createTable($array)
    {

        $array['table'] = trim($array['table']);
        $array['table'] = str_replace('{', '', $array['table']);
        $array['table'] = str_replace('}', '', $array['table']);
        $array['table'] = str_replace('%', '', $array['table']);

        $column_id = '';
        $table = 'CREATE TABLE `' . $array['table'] . '` (';
        $table .= PHP_EOL;

        foreach ($array['data'] as $index => $column) {

            $table .= "\t";
            $column[0] = str_replace('$', '', $column[0]);
            $column[1] = str_replace('$', '', $column[1]);

            $table .= "`" . $column[1] . "` ";

            if ($column[0] == 'string') {
                $table .= 'varchar(100) NULL,';
            } elseif ($column[0] == 'integer' && $index > 0) {
                $table .= 'int NULL DEFAULT \'0\',';
            } elseif ($column[0] == 'float') {
                $table .= 'decimal(10,2) NULL DEFAULT \'0\',';
            } elseif ($column[0] == 'integer' && $index == 0) {
                $column_id = $column[1];
                $table .= 'int(10) unsigned NOT NULL AUTO_INCREMENT,';
            }
            $table .= PHP_EOL;

        }

        $table .= 'PRIMARY KEY (`' . $column_id . '`) ';
        $table .= PHP_EOL;
        $table .= ') ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; ';
        $table .= PHP_EOL;

        $connection = Yii::$app->getDb();
        $add = 0;

        try {
            $db = $connection->createCommand("SELECT * FROM `" . $array['table'] . "`")->queryAll();
        } catch (Exception $exception) {
            $add = 1;

        }

        if ($add) {
            try {
                $connection->createCommand($table)->execute();
            } catch (Exception $exception) {

                echo PHP_EOL;
                echo PHP_EOL;
                echo "Not Added: " . $array['table'];
                echo PHP_EOL;

            }
        }

    }


    /**
     * @param $path
     * @return array
     */
    private function readFile($path)
    {

        $array = [
            'table' => '',
            'data' => [],
        ];

        $fn = fopen($path, "r");

        while (!feof($fn)) {
            $result = fgets($fn);

            if (strpos($result, '* @property') == TRUE) {
                $result = str_replace('* @property', '', $result);
                $result = preg_replace('/\s+/', ' ', $result);
                $result = trim($result);

                $array['data'][] = explode(' ', $result);
            }
            if (strpos($result, "return '") == TRUE) {
                $table = str_replace('return ', '', $result);
                $table = str_replace("'", '', $table);
                $table = str_replace(";", '', $table);
                $array['table'] = $table;
            }
        }

        fclose($fn);

        return $array;
    }

}
