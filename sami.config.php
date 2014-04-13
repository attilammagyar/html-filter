<?php

use Symfony\Component\Finder\Finder;


$source = Finder::create()
    ->files()
    ->name("*.php")
    ->notName("*Test.php")
    ->in(__DIR__ . "/AthosHun/HTMLFilter") ;

$options = array(
    "build_dir" => __DIR__ . "/docs/api",
    "default_opened_level" => 2,
);

return new Sami\Sami($source, $options);
