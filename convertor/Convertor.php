<?php

class Convertor
{
    /**
     * @param string $source
     * @param string $target
     * @param string $imagesDir
     * @throws Exception
     */
    public function convert(string $source, string $target, string $imagesDir)
    {
        $document = new Document($source);
        $iterator = $document->getIterator();

        $output = [
            '\documentclass{article}',
            '\usepackage[utf8]{inputenc}',

            '\title{Diplomova prace}',
            '\author{Petr Hladik}',
            '\date{April 2019}',

            '\usepackage{natbib}',
            '\usepackage{graphicx}',

            '\begin{document}',
        ];
        $images = [];
        $todos = [];
        $isItemizing = false;
        while( $iterator->valid() )
        {
            /** @var Row $row */
            $row = $iterator->current();

            if ($row->hasTODO()) {
                $todos[] = $iterator->key();
            }

            if ($row->isImage()) {
                $images[] = $row->convertImage($imagesDir);
            }

            $row->convertDashes();
            $row->convertH4();
            $row->convertH3();
            $row->convertH2();
            $row->convertH1();
            $row->convertStrong();
            $row->convertItalic();

            // <ul>
            if ($row->isUnorderedListItem()) {
                if (! $isItemizing) {
                    $isItemizing = true;
                    $output[] = '\begin{itemize}';
                }
                $row->convertUnorderedListItem();
            } elseif ($isItemizing) {
                $isItemizing = false;
                $output[] = '\end{itemize}';
            }

            $output[] = $row->getContent();
            $iterator->next();
        }

        $output[] = '\bibliographystyle{plain}';
        $output[] = '\bibliography{references}';
        $output[] = '\end{document}';

        file_put_contents($target, implode(PHP_EOL, $output));
        $this->printStats($target, $images, $todos);
        exit;
    }

    /**
     * @param string $target
     * @param array $images
     * @param array $todos
     */
    private function printStats(string $target, array $images, array $todos)
    {
        print 'CONVERTED TO `' . $target . '`' . PHP_EOL;
        print PHP_EOL;

        print 'Images count > ' . count($images) . '`' . PHP_EOL;
        if (! empty($images)) {
            foreach ($images as $img) {
                print $img . PHP_EOL;
            }
        }

        print PHP_EOL;

        print 'TODOs count > ' . count($todos) . '`' . PHP_EOL;
        if (! empty($todos)) {
            foreach ($todos as $line) {
                print 'TODO at line #' . ++$line . PHP_EOL;
            }
        }

        print PHP_EOL;
    }
}
