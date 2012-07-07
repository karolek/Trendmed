<?php
namespace Trendmed\Interfaces;
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 24.05.12
 * Time: 13:54
 * To change this template use File | Settings | File Templates.
 */
interface Photo {
    public function getFilename();
    public function setFilename($filename);
}