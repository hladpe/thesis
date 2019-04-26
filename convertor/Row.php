<?php

class Row
{
    /**
     * @var string
     */
    private $content;

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = trim($content);
    }

    /**
     * @return bool
     */
    public function hasTODO(): bool
    {
        return preg_match('/todo/i', $this->content);
    }

    /**
     * @return bool
     */
    public function isH1(): bool
    {
        return strpos($this->content, '# ') === 0;
    }

    /**
     * @return string
     */
    public function convertH1(): string
    {
        return trim(str_replace('# ', '\section{', $this->content)) . '}';
    }
}
