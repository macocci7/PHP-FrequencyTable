<?php

namespace Macocci7\PhpFrequencyTable\Traits;

trait AttributesTrait
{
    private string $lang;

    /**
     * @var array<string, array<string, string>>
     */
    private array $supportedLangs;

    private bool $showMean = false;

    /**
     * returns supported langs
     * @return  string[]
     */
    public function langs(): array
    {
        return array_keys($this->supportedLangs);
    }

    /**
     * sets lang or returns current lang
     */
    public function lang(string|null $lang = null): self|string
    {
        if (is_null($lang)) {
            return $this->lang;
        }
        if (isset($this->supportedLangs[$lang])) {
            $this->lang = $lang;
        }
        return $this;
    }

    /**
     * sets visibility of mean on
     */
    public function meanOn(): self
    {
        $this->showMean = true;
        return $this;
    }

    /**
     * sets visibility of mean off
     */
    public function meanOff(): self
    {
        $this->showMean = false;
        return $this;
    }
}
