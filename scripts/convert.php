<?php

require 'functions.php';
loadClasses();

$configuration = new Configuration(
    findThesis(),
    baseDir() . '/outputs/',
    'Diplomová práce',
    'Petr Hladík'
);
(new Convertor($configuration))->convert();