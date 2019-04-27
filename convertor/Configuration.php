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
     * @var string
     */
    private $bibliographyStyle = 'plain';

    /**
     * @var string
     */
    private $bibliography = 'references';

    /**
     * @var array
     */
    private $citations = [];

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

    /**
     * @return array
     */
    public function getCitations(): array
    {
        return $this->citations;
    }


    /**
     * @param string $hash
     * @param string $citations
     * @return Configuration
     */
    public function addCitation(string $hash, string $citations): Configuration
    {
        $this->citations[$hash] = $citations;
        return $this;
    }

    /**
     * @param array $citations
     * @return Configuration
     */
    public function setCitations(array $citations): Configuration
    {
        $this->citations = $citations;
        return $this;
    }
}
