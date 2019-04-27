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
    private $title = '';

    /**
     * @var string
     */
    private $author = '';

    /**
     * @var array
     */
    private $citations = [];

    /**
     * @param string $inputFile
     * @param string $outputDirectory
     * @param string $title
     * @param string $author
     */
    public function __construct(
        string $inputFile,
        string $outputDirectory,
        string $title,
        string $author
    ) {
        $this->inputFile = $inputFile;
        $this->outputDirectory = $outputDirectory;
        $this->title = $title;
        $this->author = $author;
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
