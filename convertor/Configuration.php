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
     * @var array
     */
    private $citations = [];

    /**
     * @param string $inputFile
     * @param string $outputDirectory
     */
    public function __construct(
        string $inputFile,
        string $outputDirectory
    ) {
        $this->inputFile = $inputFile;
        $this->outputDirectory = $outputDirectory;
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
