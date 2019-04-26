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

/**
 * @param string $path
 */
function exportHeaders(string $path)
{
    $content = file_get_contents($path);
    preg_match_all("/^#(.*)$/m", $content,$matches);
    $headers = $matches[0];

    $output = '<!DOCTYPE html><html lang="en"><head><title>Thesis structure</title><style>* {margin: 0;} span {opacity: 0.1;}</style></head><body>' . PHP_EOL;
    foreach ($headers as $header)
    {
        switch (true) {
            case (strpos($header, '#####') !== false):
                $header = str_replace('#####', '<span>&xmap;&xmap;&xmap;&xmap;&xmap;</span>', $header) . '<br>';
                break;
            case (strpos($header, '####') !== false):
                $header = str_replace('####', '<span>&xmap;&xmap;&xmap;&xmap;</span>', $header) . '<br>';
                break;
            case (strpos($header, '###') !== false):
                $header = str_replace('###', '<span>&xmap;&xmap;&xmap;</span>', $header) . '<br>';
                break;
            case (strpos($header, '##') !== false):
                $header = str_replace('##', '<span>&xmap;&xmap;</span>', $header) . '<br>';
                break;
            default:
                $header = str_replace('#', '<span>&xmap;</span><strong>', $header) . '</strong><br>';
                break;
        }
        $output .= $header . PHP_EOL;
    }
    $output .= '</body></html>';

    file_put_contents(baseDir() . '/outputs/headers.html', $output);
    echo 'DONE';
}