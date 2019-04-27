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

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function hasTODO(): bool
    {
        return preg_match('/todo/i', $this->content);
    }

    public function convertH1(): void
    {
        if (strpos($this->content, '# ') === 0) {
            $this->content = trim(str_replace('# ', '\section{', $this->content)) . '}';
        }
    }

    public function convertH2(): void
    {
        if (strpos($this->content, '## ') === 0) {
            $this->content = trim(str_replace('## ', '\subsection{', $this->content)) . '}';
        }
    }

    public function convertH3(): void
    {
        if (strpos($this->content, '### ') === 0) {
            $this->content = trim(str_replace('### ', '\subsubsection{', $this->content)) . '}';
        }
    }

    public function convertH4(): void
    {
        if (strpos($this->content, '#### ') === 0) {
            $this->content = trim(str_replace('#### ', '\textbf{', $this->content)) . '}';
        }
    }

    public function convertStrong(): void
    {
        preg_match_all('/\*\*(.*?)\*\*/', $this->content, $matches);

        if (! empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $this->content = str_replace('**' . $match . '**', '\textbf{' . $match . '}', $this->content);
            }
        }
    }

    public function convertItalic(): void
    {
        preg_match_all('/\_(.*?)\_/', $this->content, $matches);

        if (! empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $this->content = str_replace('_' . $match . '_', '\textit{' . $match . '}', $this->content);
            }
        }
    }

    /**
     * @return bool
     */
    public function isUnorderedListItem(): bool
    {
        return strpos($this->content, '-') === 0;
    }

    public function convertUnorderedListItem(): void
    {
        $this->content = ltrim($this->content, '-');
        $this->content = '\item ' . trim($this->content);
    }

    public function isImage(): bool
    {
        return strpos($this->content, '![') === 0;
    }

    /**
     * @param string $imagesDir
     * @return string
     * @throws Exception
     */
    public function convertImage(string $imagesDir): string
    {
        preg_match('/\!\[(.*?)\]\((.*?)\)/', $this->content, $matches);
        $title = $matches[1];
        $image = $matches[2];
        $hash = md5($image);
        $path = $imagesDir . DIRECTORY_SEPARATOR . md5($image) . '.' . pathinfo($image, PATHINFO_EXTENSION);
        file_exists($imagesDir) or mkdir($imagesDir);

        if (! is_dir($imagesDir)) {
            throw new Exception('Images path is not directory.');
        }

        file_put_contents($path, file_get_contents($image));
        $this->content = '\begin{image}[h]' . PHP_EOL
                        . '\includegraphics{' . $hash . '}' . PHP_EOL
                        . '\caption{' . $title . '}' . PHP_EOL
                        . '\end{image}';

        return $hash;
    }

    public function convertDashes(): void
    {
        $this->content = str_replace(' - ', ' – ', $this->content);
    }

    /**
     * @return array
     */
    public function convertCitations(): array
    {
        preg_match_all('/\[cit\](.*?)\[\/cit\]/', $this->content, $matches);
        if (empty($matches[1])) {
            return [];
        }

        $citations = [];
        foreach ($matches[1] as $match) {
            $hash = md5(trim($match));
            $citations[$hash] = trim($match);

            $this->content = str_replace('[cit]' . $match . '[/cit]', '\cite{' . $hash . '}', $this->content);
        }

        return $citations;
    }
}
