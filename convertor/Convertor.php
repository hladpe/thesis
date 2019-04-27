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
     * @var bool
     */
    private $isItemizing = false;

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

        foreach ($configuration->getPackagesUtf8() as $package) {
            $this->output[] = '\usepackage[utf8]{' . $package . '}';
        }

        $this->output[] = '\title{' . $configuration->getTitle() . '}';
        $this->output[] = '\author{' . $configuration->getAuthor() . '}';
        $this->output[] = '\date{' . $configuration->getDate() . '}';

        foreach ($configuration->getPackages() as $package) {
            $this->output[] = '\usepackage{' . $package . '}';
        }
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

            if ($row->hasTODO()) {
                $this->todos[] = $iterator->key();
            }

            if ($row->isImage()) {
                $this->images[] = $row->convertImage($this->outputImagesPath);
            }

            $row->convertDashes();
            $row->convertH4();
            $row->convertH3();
            $row->convertH2();
            $row->convertH1();
            $row->convertStrong();
            $row->convertItalic();

            $this->citations = array_merge($this->citations, $row->convertCitations());

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

            $this->output[] = $row->getContent();
            $iterator->next();

            $this->progressBar($iterator->key(), $iterator->count());
        }

        $this->output[] = '\bibliographystyle{' . $this->configuration->getBibliographyStyle() .'}';
        $this->output[] = '\bibliography{' . $this->configuration->getBibliography() . '}';

        $this->output[] = '\end{document}';

        file_put_contents($this->outputFilePath, implode(PHP_EOL, $this->output));
        $this->printStats();
        exit;
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
    private function getOutputImagesPath(): string
    {
        $dir = $this->getOutputDir() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;

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

        print 'Images count = ' . count($this->images) . '`' . PHP_EOL;
        if (! empty($this->images)) {
            foreach ($this->images as $img) {
                print $img . PHP_EOL;
            }
        }

        print PHP_EOL;

        print 'Citations count = ' . count($this->citations) . '`' . PHP_EOL;
        if (! empty($this->citations)) {
            foreach ($this->citations as $hash => $citation) {
                print '[' . $hash . '] ' . $citation . PHP_EOL;
            }
        }

        print PHP_EOL;

        print 'TODOs count = ' . count($this->todos) . '`' . PHP_EOL;
        if (! empty($this->todos)) {
            foreach ($this->todos as $line) {
                print 'TODO at line #' . ++$line . PHP_EOL;
            }
        }

        print PHP_EOL;
    }
}
