<?php

class Configuration
{
    /**
     * @var string
     */
    private $inputFile = '';

    /**
     * @var string
     */
    private $outputDirectory = '';

    /**
     * @var string
     */
    private $documentClass = 'article';

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $author = '';

    /**
     * @var string
     */
    private $date = '';

    /**
     * @var array
     */
    private $packages = ['natbib', 'graphicx'];

    /**
     * @var array
     */
    private $packagesUtf8 = ['inputenc'];

    /**
     * @var string
     */
    private $bibliographyStyle = 'plain';

    /**
     * @var string
     */
    private $bibliography = 'references';

    /**
     * @param string $inputFile
     * @param string $outputDirectory
     * @param string $title
     * @param string $author
     * @param string|null $date
     */
    public function __construct(
        string $inputFile,
        string $outputDirectory,
        string $title,
        string $author,
        ?string $date = null
    ) {
        $this->inputFile = $inputFile;
        $this->outputDirectory = $outputDirectory;
        $this->title = $title;
        $this->author = $author;
        $this->date = $date ?? date('F Y');
    }

    /**
     * @return string
     */
    public function getInputFile(): string
    {
        return $this->inputFile;
    }

    /**
     * @return string
     */
    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }

    /**
     * @return string
     */
    public function getDocumentClass(): string
    {
        return $this->documentClass;
    }

    /**
     * @param string $documentClass
     * @return Configuration
     */
    public function setDocumentClass(string $documentClass): Configuration
    {
        $this->documentClass = $documentClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return false|string|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return array
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * @param array $packages
     * @return Configuration
     */
    public function setPackages(array $packages): Configuration
    {
        $this->packages = $packages;
        return $this;
    }

    /**
     * @param string $package
     * @return Configuration
     */
    public function addPackage(string $package): Configuration
    {
        $this->packages = $package;
        return $this;
    }

    /**
     * @return array
     */
    public function getPackagesUtf8(): array
    {
        return $this->packagesUtf8;
    }

    /**
     * @param array $packagesUtf8
     * @return Configuration
     */
    public function setPackagesUtf8(array $packagesUtf8): Configuration
    {
        $this->packagesUtf8 = $packagesUtf8;
        return $this;
    }

    /**
     * @param string $packageUtf8
     * @return Configuration
     */
    public function addPackageUtf8(string $packageUtf8): Configuration
    {
        $this->packagesUtf8[] = $packageUtf8;
        return $this;
    }

    /**
     * @return string
     */
    public function getBibliographyStyle(): string
    {
        return $this->bibliographyStyle;
    }

    /**
     * @param string $bibliographyStyle
     * @return Configuration
     */
    public function setBibliographyStyle(string $bibliographyStyle): Configuration
    {
        $this->bibliographyStyle = $bibliographyStyle;
        return $this;
    }

    /**
     * @return string
     */
    public function getBibliography(): string
    {
        return $this->bibliography;
    }

    /**
     * @param string $bibliography
     * @return Configuration
     */
    public function setBibliography(string $bibliography): Configuration
    {
        $this->bibliography = $bibliography;
        return $this;
    }
}
