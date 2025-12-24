<?php
namespace Saithink\LaravelOrmLog;

use Webman\Bootstrap;
use support\Db;
use support\Log;

/**
 * 日志记录类
 */
class BootstrapLog implements Bootstrap
{
    public static function start($worker)
    {
        $config = config('plugin.saithink.laravelorm-log.app', [
            'enable' => true,
            'console'   => false,
            'file'  => true,
        ]);
        if (!$config['enable']) {
            return;
        }
        // 进行监听处理
        Db::listen(function($query) use ($config) {
            $sql = $query->sql;
            $time = $query->time;
            if ($sql === 'select 1') {
                // 心跳
                return;
            }
            $bindings = [];
            if ($query->bindings) {
                foreach ($query->bindings as $v) {
                    if (is_numeric($v)) {
                        $bindings[] = $v;
                    } else {
                        $bindings[] = '"' . strval($v) . '"';
                    }
                }
            }
            $sql = self::replacePlaceholders($sql, $bindings);
            $log = $sql." [{$time}ms]";
            // 打印到控制台
            if ($config['console']) {
                echo PHP_EOL ."\033[35m" . "EloquentORM: " . date("Y-m-d H:i:s") . "\033[0m";
                echo PHP_EOL . "[SQL]" . "\033[32m" .$sql . "\033[0m";
                if ($time < 50) {
                    echo PHP_EOL . "[Time]" . "\033[32m" . $time . "ms" . "\033[0m";
                } else if ($time < 200) {
                    echo PHP_EOL . "[Time]" ."\033[33m" . $time . "ms" . "\033[0m";
                } else  {
                    echo PHP_EOL . "[Time]" . "\033[31m" . $time . "ms" . "\033[0m";
                }
            }
            // 记录到日志文件
            if ($config['file']) {
                Log::channel('plugin.saithink.laravelorm-log.sql')->info($log);
            }
        });
    }

    /**
     * 字符串处理
     * @param $sql
     * @param $params
     * @return mixed|string
     */
    public static function replacePlaceholders($sql, $params) {
        if (empty($params)) {
            return $sql;
        }
        $parts = explode('?', $sql);
        $result = $parts[0];
        $paramCount = count($params);
        for ($i = 0; $i < $paramCount && $i < count($parts) - 1; $i++) {
            $value = $params[$i];
            $result .= $value . $parts[$i + 1];
        }
        if (count($parts) - 1 > $paramCount) {
            $result .= implode('?', array_slice($parts, $paramCount + 1));
        }
        return $result;
    }
}