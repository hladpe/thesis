<?php

class Document
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->load();
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->rows);
    }

    private function load()
    {
        $data = file_get_contents($this->path);
        foreach (explode(PHP_EOL, $data) as $row) {
            $this->rows[] = new Row($row);
        }
    }
}
