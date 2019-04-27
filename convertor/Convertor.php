<?php

class Convertor
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var string
     */
    private $outputFilePath;

    /**
     * @var string
     */
    private $outputImagesPath;

    /**
     * @var array
     */
    private $output = [];

    /**
     * @var array
     */
    private $images = [];

    /**
     * @var array
     */
    private $todos = [];

    /**
     * @var array
     */
    private $citations = [];

    /**
     * @var array
     */
    private $unresolvedCitations = [];

    /**
     * @var bool
     */
    private $isItemizing = false;

    /**
     * @var bool
     */
    private $isTabling = false;

    /**
     * @param Configuration $configuration
     * @throws Exception
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->outputFilePath = $this->getOutputFilePath();
        $this->outputImagesPath = $this->getOutputImagesPath();

        $this->output[] = '\documentclass{' . $configuration->getDocumentClass() . '}';

        $this->output[] = '\title{' . $configuration->getTitle() . '}';
        $this->output[] = '\author{' . $configuration->getAuthor() . '}';
        $this->output[] = '\date{' . $configuration->getDate() . '}';

        $this->output[] = '\usepackage{natbib}';
        $this->output[] = '\usepackage{graphicx}';
        $this->output[] = '\usepackage[utf8]{inputenc}';
        $this->output[] = '\usepackage[czech]{babel}';
        $this->output[] = '\usepackage{tabu}';
        $this->output[] = '\usepackage{float}';
        $this->output[] = '\usepackage[backend=biber,style=alphabetic]{biblatex}';
        $this->output[] = '\graphicspath{ {./images/} }';
        $this->output[] = '\addbibresource{' . $this->getOutputBibFileName() . '}';
    }

    /**
     * @throws Exception
     */
    public function convert()
    {
        $this->output[] = '\begin{document}';

        $document = new Document($this->configuration->getInputFile());
        $iterator = $document->getIterator();

        while( $iterator->valid() )
        {
            /** @var Row $row */
            $row = $iterator->current();

            $this->citations = array_merge($this->citations, $row->convertCitations());

            if ($row->hasTODO()) {
                $this->todos[] = $iterator->key();
            }

            if ($row->isImage()) {
                $this->images[] = $row->convertImage($this->outputImagesPath);
            }

            $row->escapePercent();
            $row->convertDashes();
            $row->convertH4();
            $row->convertH3();
            $row->convertH2();
            $row->convertH1();
            $row->convertStrong();
            $row->convertItalic();
            $row->convertQuotes();

            // <ul>
            if ($row->isUnorderedListItem()) {
                if (! $this->isItemizing) {
                    $this->isItemizing = true;
                    $this->output[] = '\begin{itemize}';
                }
                $row->convertUnorderedListItem();
            } elseif ($this->isItemizing) {
                $this->isItemizing = false;
                $this->output[] = '\end{itemize}';
            }

            // <table>
            if ($row->isTableRow()) {
                if (! $this->isTabling) {
                    $this->isTabling = true;
                    $this->output[] = '\begin{tabu} to 0.75\textwidth { | ' . implode(' | ', array_fill(0, $row->getTableRowColsCount(), 'X[l]')). ' | }';
                }
                if ($row->isEmptyTableRow()) {
                    $iterator->next();
                    continue;
                }
                $row->convertTableRow();
                $this->output[] = '\hline';
            } elseif ($this->isTabling) {
                $this->isTabling = false;
                $this->output[] = '\hline';
                $this->output[] = '\end{tabu}';
            }

            $this->output[] = $row->getContent();
            $iterator->next();

            $this->progressBar($iterator->key(), $iterator->count());
        }

        $this->output[] = '\bibliographystyle{' . $this->configuration->getBibliographyStyle() .'}';
        $this->output[] = '\bibliography{' . $this->configuration->getBibliography() . '}';

        $this->output[] = '\end{document}';

        file_put_contents($this->outputFilePath, implode(PHP_EOL, $this->output));

        print PHP_EOL . PHP_EOL . 'CONVERTED.';
        $this->resolveCitations();
        $this->printStats();
        exit;
    }

    /**
     * @throws Exception
     */
    private function resolveCitations()
    {
        print PHP_EOL . 'RESOLING CITATIONS';

        $jsonBibPath = $this->getOutputBibJsonPath();
        if (file_exists($jsonBibPath)) {
            $citations = (array) json_decode(file_get_contents($jsonBibPath));
        } else {
            $citations = [];
        }

        $count = count($this->citations);
        $i = 1;

        foreach ($this->citations as $hash => $citation) {

            if (array_key_exists($hash, $citations)) {
                continue;
            }

            try {
                $citations[$hash] = $this->resolveCitation($hash, $citation);
            } catch (Exception $e) {
                $this->unresolvedCitations[] = $e->getMessage();
            }

            $this->progressBar($i, $count);
            ++$i;
        }

        file_put_contents($jsonBibPath, json_encode($citations));
        file_put_contents($this->getOutputBibPath(), implode(PHP_EOL . PHP_EOL, $citations));
    }

    /**
     * @param string $hash
     * @param string $citation
     * @return string
     * @throws Exception
     */
    private function resolveCitation(string $hash, string $citation)
    {
        if (array_key_exists($hash, $this->configuration->getCitations())) {
            return $this->configuration->getCitations()[$hash];
        }

        preg_match('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $citation, $matches);

        if (empty($matches)) {
            throw new Exception('Cant get citation `' . $hash . '`, `' . $citation . '`');
        }

        $url = $matches[0];

        $page = @file_get_contents($url);
        preg_match('/\<title\>(.*?)\<\/title\>/si', $page, $matches);

        if (empty($matches)) {
            throw new Exception('Cant get citation `' . $hash . '`, `' . $citation . '`');
        }

        $title = trim($matches[1]);
        $title = @iconv(mb_detect_encoding($title, mb_detect_order(), true), "UTF-8", $title);
        $title = str_replace(PHP_EOL, '', $title);
        $title = str_replace(" \t", ' ', $title);
        $title = str_replace('  ', ' ', $title);
        $title = str_replace('  ', ' ', $title);
        $title = str_replace('  ', ' ', $title);
        $title = str_replace('  ', ' ', $title);

       return '@online{' . $hash . ',' . PHP_EOL
        . '    title     = "' . $title . '",' . PHP_EOL
        . '    url       = "' . $url . '",' . PHP_EOL
        . '    note      = "[Online; citováno ' . date('d.m.Y') . ']"' . PHP_EOL
        . '}' . PHP_EOL;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getOutputFilePath(): string
    {
        $name = $this->webalize($this->configuration->getAuthor() . '-' . $this->configuration->getTitle()) . '.tex';
        return $this->getOutputDir() . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getOutputBibPath(): string
    {
        return $this->getOutputDir() . DIRECTORY_SEPARATOR . $this->getOutputBibFileName();
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getOutputBibFileName(): string
    {
        return $this->webalize($this->configuration->getAuthor() . '-' . $this->configuration->getTitle()) . '.bib';
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getOutputBibJsonPath(): string
    {
        $name = $this->webalize($this->configuration->getAuthor() . '-' . $this->configuration->getTitle()) . '.json';
        return $this->getOutputDir() . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getOutputImagesPath(): string
    {
        $dir = $this->getOutputDir() . DIRECTORY_SEPARATOR . 'obrazky-figures' . DIRECTORY_SEPARATOR;

        if (! file_exists($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getOutputDir(): string
    {
        $dir = realpath($this->configuration->getOutputDirectory());
        if (! $dir) {
            throw new Exception('Output directory `' . $this->configuration->getOutputDirectory() . '` does not exist!');
        }

        if (! is_dir($this->configuration->getOutputDirectory())) {
            throw new Exception('Output directory `' . $this->configuration->getOutputDirectory() . '` is not a directory.');
        }

        return $dir;
    }

    /**
     * @param string $string
     * @return string
     */
    private function webalize(string $string)
    {
        $string = preg_replace('~[^\\pL0-9_]+~u', '-', $string);
        $string = trim($string, "-");
        $string = iconv("utf-8", "us-ascii//TRANSLIT", $string);
        $string = strtolower($string);
        $string = preg_replace('~[^-a-z0-9_]+~', '', $string);
        return (string) $string;
    }

    function progressBar($done, $total) {
        $perc = floor(($done / $total) * 100);
        $left = 100 - $perc;
        $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc%% - $done/$total", "", "");
        fwrite(STDERR, $write);
    }

    /**
     * Prints convertion stats
     */
    private function printStats()
    {
        print PHP_EOL . PHP_EOL . 'CONVERTED TO `' . $this->outputFilePath . '`' . PHP_EOL;
        print PHP_EOL;

        print 'TODOs count = ' . count($this->todos) . PHP_EOL;
        if (! empty($this->todos)) {
            foreach ($this->todos as $line) {
                print 'TODO at line #' . ++$line . PHP_EOL;
            }
        }

        print PHP_EOL;

        print 'UNRESOLVED CITATIONS count = ' . count($this->unresolvedCitations) . PHP_EOL;
        if (! empty($this->unresolvedCitations)) {
            foreach ($this->unresolvedCitations as $citation) {
                print $citation . PHP_EOL;
            }
        }

        print PHP_EOL;
    }
}
