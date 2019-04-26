<?php

class Convertor
{
    public function convert(string $source, string $target)
    {
        $document = new Document($source);
        $iterator = $document->getIterator();

        $output = [];
        $todos = [];
        while( $iterator->valid() )
        {
            /** @var Row $row */
            $row = $iterator->current();

            if ($row->hasTODO()) {
                $todos[] = $iterator->key();
            }

            if ($row->isH1()) {
                $output[] = $row->convertH1();
                $iterator->next();
                continue;
            }

            $iterator->next();
        }

        file_put_contents($target, implode(PHP_EOL, $output));
        print 'CONVERTED TO `' . $target . '`' . PHP_EOL;
        print 'TODOs count > ' . count($todos) . '`' . PHP_EOL;
        if (! empty($todos)) {
            foreach ($todos as $line) {
                print 'TODO at line #' . ++$line . PHP_EOL;
            }
        }

        print PHP_EOL;
    }
}
