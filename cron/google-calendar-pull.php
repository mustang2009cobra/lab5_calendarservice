<?php

use Doctrine\Common\ClassLoader;

require './Doctrine/Doctrine/Common/ClassLoader.php';

$classLoader = new ClassLoader('Doctrine', './Doctrine');
$classLoader->register();


//Script goes here


?>