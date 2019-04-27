<?php

/**
 * @return mixed
 * @throws Exception
 */
function findThesis()
{
    foreach (glob(baseDir() . '/*.md') as $file) {
        return $file;
    }

    throw new Exception('No result! o.O');
}

/**
 * @return string
 */
function baseDir(): string
{
    return realpath(__DIR__ . '/../');
}

function loadClasses()
{
    foreach (glob(baseDir() . '/convertor/*.php') as $file) {
        require_once $file;
    }
}