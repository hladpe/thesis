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
     * @var string
     */
    private $outputImagesDirName;

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
     * @var bool
     */
    private $isNewLining = false;

    /**
     * @param Configuration $configuration
     * @throws Exception
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->outputFilePath = $this->getOutputFilePath();
        $this->outputImagesPath = $this->getOutputImagesPath();
        $this->outputImagesDirName = $this->getOutputImagesDirName();
    }

    /**
     * @throws Exception
     */
    public function convert()
    {
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
                $this->images[] = $row->convertImage($this->outputImagesPath, $this->outputImagesDirName);
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

            // new line - after two new lines in a row
            if ($row->isEmptyLine()) {
                if ($this->isNewLining) {
                    $this->output[] = '\bigbreak';
                } else {
                    $this->isNewLining = true;
                }
                $this->output[] = '';
            } else {
                $this->isNewLining = false;
                $this->output[] = $row->getContent();
            }

            $iterator->next();

            $this->progressBar($iterator->key(), $iterator->count());
        }

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

        $i = 1;
        $usedCitations = [];
        $count = count($this->citations);

        foreach ($this->citations as $hash => $citation) {

            if (array_key_exists($hash, $citations)) {
                $usedCitations[] = $citations[$hash];
                continue;
            }

            try {
                $resolved = $this->resolveCitation($hash, $citation);
                $citations[$hash] = $resolved;
                $usedCitations[] = $resolved;
            } catch (Exception $e) {
                $this->unresolvedCitations[] = $e->getMessage();
            }

            $this->progressBar($i, $count);
            ++$i;
        }

        file_put_contents($jsonBibPath, json_encode($citations));
        file_put_contents($this->getOutputBibPath(), implode(PHP_EOL . PHP_EOL, $usedCitations));
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
        
        // $tags = (array) @get_meta_tags($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $output = curl_exec($ch);
        $output = mb_convert_encoding($output, 'UTF-8', mb_detect_encoding($output, 'UTF-8, ISO-8859-1', true));

        $pattern = '/[<]title[>]([^<]*)[<][\/]title[>]/i';
        preg_match($pattern, $output, $matches);

        if (empty($matches)) {
            throw new Exception('Cant get citation `' . $hash . '`, `' . $citation . '`');
        }

        $httpcode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $title = trim(strip_tags($matches[0]));
        $title = str_replace(PHP_EOL, ' ', $title);
        $title = str_replace("\t", ' ', $title);
        $title = str_replace('  ', ' ', $title);
        $title = str_replace('  ', ' ', $title);
        $title = str_replace('  ', ' ', $title);
        $title = html_entity_decode($title);
        $title = htmlspecialchars_decode($title);
        $title = trim($title);
        $title = ucfirst($title);

        if ($httpcode !== 200) {
            throw new Exception('Cant get citation `' . $hash . '`, `' . $citation . '` (reason: HTTPS result code: ' . $httpcode . ')');
        }

        if (! $title) {
            throw new Exception('Cant get citation `' . $hash . '`, `' . $citation . '`');
        }

        $data = file_get_contents('http://archive.org/wayback/available?url=' . $url . '&timestamp=19900101');
        $data = json_decode($data, true);

        if (! empty($data['archived_snapshots']['closest']['timestamp'])) {
            $year = substr($data['archived_snapshots']['closest']['timestamp'], 0, 4) . ' ';
        } else {
            $year = '';
        }

        $cit = [];
        $cit['title'] = '"' . $this->mbUcfirst(mb_strtolower($title), 'utf-8') . '"';
        // $cit['url'] = '"' . $url . '"';

        /*
        if (array_key_exists('author', $tags)) {
            $author = trim($tags['author']);
            $author = html_entity_decode($author);
            $author = htmlspecialchars_decode($author);

            if (preg_match('/^[\p{Latin}]+$/u', $author)) {
                $cit['author'] = '"' . $author . '"';
            }
        }
        */

        $urlParts = parse_url($url);
        $host = str_replace('www.', '', $urlParts['host']);
        $escaped = str_replace('_', '\\_', $url);

        $cit['note'] = '"\textit{' . ucfirst($host). '} [online]. ' . $year . '[cit. ' . date('d-m-Y') . ']. Dostupné z: ' . $escaped . '"';

        $string = '';
        foreach ($cit as $key => $value) {
            $string .= PHP_EOL . $key . ' = ' . $value . ',';
        }

        return '@misc{' . $hash . ',' . $string . PHP_EOL . '}' . PHP_EOL;

    }

    private function mbUcfirst($string, $encoding)
    {
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, $strlen - 1, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
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
        $dir = $this->getOutputDir() . DIRECTORY_SEPARATOR . $this->getOutputImagesDirName() . DIRECTORY_SEPARATOR;

        if (! file_exists($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    private function getOutputImagesDirName(): string
    {
        return 'obrazky-figures';
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
