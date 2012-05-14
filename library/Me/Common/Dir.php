<?php
namespace Me\Common;
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 12.05.12
 * Time: 12:59
 * To change this template use File | Settings | File Templates.
 */
class Dir
{
    /**
     * Deletes given dir recursivly
     *
     * @param type $dir
     * @return void
     */
    public static function rrmdir($dir) {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file))
                self::rrmdir($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }

}
