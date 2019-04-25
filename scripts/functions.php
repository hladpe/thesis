<?php

/**
 * @return mixed
 * @throws Exception
 */
function findThesis()
{
    $path = realpath(__DIR__ . '/../');
    foreach (glob($path . '/*.md') as $file) {
        return $file;
    }

    throw new Exception('No result! o.O');
}

/**
 * @param string $path
 */
function exportHeaders(string $path)
{
    $content = file_get_contents($path);

    echo $content;
    die();
}