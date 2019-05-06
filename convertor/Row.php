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

    public function isEmptyLine(): bool
    {
        return trim($this->content) === '';
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
            $this->content = trim(str_replace('# ', '\chapter{', $this->content)) . '}';
        }
    }

    public function convertH2(): void
    {
        if (strpos($this->content, '## ') === 0) {
            $this->content = trim(str_replace('## ', '\section{', $this->content)) . '}';
        }
    }

    public function convertH3(): void
    {
        if (strpos($this->content, '### ') === 0) {
            $this->content = trim(str_replace('### ', '\subsection{', $this->content)) . '}';
        }
    }

    public function convertH4(): void
    {
        if (strpos($this->content, '#### ') === 0) {
            $this->content = trim(str_replace('#### ', '\subsubsection{\textbf{', $this->content)) . '}}';
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

    public function convertQuotes(): void
    {
        preg_match_all('/\„(.*?)\“/', $this->content, $matches);

        if (! empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $this->content = str_replace('„' . $match . '“', '\uv{' . $match . '}', $this->content);
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
     * @param string $imagesDirName
     * @return string
     * @throws Exception
     */
    public function convertImage(string $imagesDir, string $imagesDirName = ''): string
    {
        preg_match('/\!\[(.*?)\]\((.*?)\)/', $this->content, $matches);
        $title = $matches[1];
        $image = $matches[2];
        $image = preg_replace('/\?.*/', '', $image);
        $hash = md5($image);
        $path = $imagesDir . DIRECTORY_SEPARATOR . md5($image) . '.' . pathinfo($image, PATHINFO_EXTENSION);
        file_exists($imagesDir) or mkdir($imagesDir);

        if (! is_dir($imagesDir)) {
            throw new Exception('Images path is not directory.');
        }

        if (! file_exists($path)) {
            file_put_contents($path, file_get_contents($image));
        }

        $titleParts = explode('||', $title);
        $title = trim($titleParts[0]);
        $style = array_key_exists(1, $titleParts) ? trim($titleParts[1]) : 'width=1.0\textwidth';

        $titleParts = explode(', zdroj:', $title);
        $justTitle = trim($titleParts[0]);

        $this->content = '\begin{figure}[H]' . PHP_EOL
                        . '\begin{center}' . PHP_EOL
                        . '\includegraphics[' . $style . ']{' . ($imagesDirName ? $imagesDirName . '/' : '') . $hash . '}' . PHP_EOL
                        . '\caption[' . $justTitle . ']{' . $title . '}' . PHP_EOL
                        . '\end{center}' . PHP_EOL
                        . '\end{figure}';

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

    public function escapePercent(): void
    {
        $this->content = str_replace('%', '\%', $this->content);
    }

    /**
     * @return bool
     */
    public function isTableRow(): bool
    {
        return strpos($this->content, '|') === 0;
    }

    /**
     * @return int
     */
    public function getTableRowColsCount(): int
    {
        return substr_count($this->content, '|') - 1;
    }

    public function isEmptyTableRow(): bool
    {
        return trim(str_replace(['|', '-'], '', $this->content)) === '';
    }

    public function convertTableRow(): void
    {
        $this->content = trim($this->content, '|');
        $this->content = str_replace('|', ' & ', $this->content);
        $this->content = str_replace('  ', ' ', $this->content);
        $this->content .= ' \\\\';
    }

    public function trimWhitespaces(): void
    {
        $this->content = trim($this->content);
    }

    public function convertDoubleSpace(): void
    {
        $this->content = str_replace('  ', ' ', $this->content);
    }

    public function convertBadSpace(): void
    {
        $this->content = str_replace('( ', '(', $this->content);
        $this->content = str_replace(' )', ')', $this->content);
        $this->content = str_replace(' .', '.', $this->content);
        $this->content = str_replace(' ,', ',', $this->content);
    }

    public function convertIndivisible(): void
    {
        $this->convertClutches();
        $this->convertSuperClutches();
        $this->convertPercents();
        $this->convertNumbers();
        $this->convertCurrencies();
    }

    private function convertClutches(): void
    {
        $list = ['k', 's', 'v', 'z', 'o', 'u', 'a', 'i'];
        foreach ($list as $item) {
            $this->content = str_replace(' ' . $item . ' ', ' ' . $item . '~', $this->content);
            $item = strtoupper($item);
            $this->content = str_replace(' ' . $item . ' ', ' ' . $item . '~', $this->content);
        }
    }

    private function convertSuperClutches(): void
    {
        $list = ['tj.', 'tzv.', 'tzn.', 'např.'];
        foreach ($list as $item) {
            $this->content = str_replace(' ' . $item . ' ', ' ' . $item . '~', $this->content);
        }
    }

    private function convertPercents(): void
    {
        $x = preg_match_all('/[0-9] %/', $this->content, $matches);
        if ($x) {
            foreach ($matches[0] as $match) {
                $replace = str_replace(' ', '~', $match);
                $this->content = str_replace($match, $replace, $this->content);
            }
        }
    }

    private function convertNumbers(): void
    {
        $x = preg_match_all('/[0-9] [0-9]/', $this->content, $matches);
        if ($x) {
            foreach ($matches[0] as $match) {
                $replace = str_replace(' ', '~', $match);
                $this->content = str_replace($match, $replace, $this->content);
            }
        }
    }

    private function convertCurrencies(): void
    {
        $x = preg_match_all('/[0-9] bil/', $this->content, $matches);
        if ($x) {
            foreach ($matches[0] as $match) {
                $replace = str_replace(' ', '~', $match);
                $this->content = str_replace($match, $replace, $this->content);
            }
        }

        $x = preg_match_all('/[0-9] mil/', $this->content, $matches);
        if ($x) {
            foreach ($matches[0] as $match) {
                $replace = str_replace(' ', '~', $match);
                $this->content = str_replace($match, $replace, $this->content);
            }
        }

        $x = preg_match_all('/[0-9] mld/', $this->content, $matches);
        if ($x) {
            foreach ($matches[0] as $match) {
                $replace = str_replace(' ', '~', $match);
                $this->content = str_replace($match, $replace, $this->content);
            }
        }

        $x = preg_match_all('/[0-9] tis/', $this->content, $matches);
        if ($x) {
            foreach ($matches[0] as $match) {
                $replace = str_replace(' ', '~', $match);
                $this->content = str_replace($match, $replace, $this->content);
            }
        }

        $x = preg_match_all('/[0-9] Kč/', $this->content, $matches);
        if ($x) {
            foreach ($matches[0] as $match) {
                $replace = str_replace(' ', '~', $match);
                $this->content = str_replace($match, $replace, $this->content);
            }
        }

        $x = preg_match_all('/[0-9] dol/', $this->content, $matches);
        if ($x) {
            foreach ($matches[0] as $match) {
                $replace = str_replace(' ', '~', $match);
                $this->content = str_replace($match, $replace, $this->content);
            }
        }
    }
}
