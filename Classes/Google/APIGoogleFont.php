<?php


namespace WEBFONTS\Webfonts\Google;


use WEBFONTS\Webfonts\Exception\WebfontsException;

class APIGoogleFont implements APIGoogleFontIF
{

    /*
     * GoogleFontVariant[]
     */
    private $variants;
    private $subsets;
    private $data;
    private $id;
    private $family ;
    private $category;
    private $version;
    private $popularity;
    private $defSubset;
    private $defVariant;

    public function __construct($apiFontData)
    {
        $this->data = $apiFontData;
        $this->id = $apiFontData['id'];
        $this->family = $apiFontData['family'];
        $this->variants = $apiFontData['variants'];
        $this->subsets = $apiFontData['subsets'];
        $this->category = $apiFontData['category'];
        $this->version = $apiFontData['version'];
        $this->popularity = $apiFontData['popularity'];
        $this->defSubset = $apiFontData['defSubset'];
        $this->defVariant = $apiFontData['defVariant'];
    }

    public function hasItalic(): bool
    {
        foreach ($this->variants as $variant) {
            if (strpos($variant, 'italic') !== false) {
                return true;
            }
        }
        return false;
    }

    public function hasNormal(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id(),
            'family' => $this->family(),
            'variants' => $this->variants(),
            'subsets' => $this->subsets(),
            'category' => $this->category(),
            'version' => $this->version(),
            'defSubset' => $this->defSubset(),
            'defVariant' => $this->defVariant(),
            'provider' => $this->provider(),
            'installation' => GoogleFontInstallationManager::getInstance()->installDetails($this->id, $this->provider()),
            'cdn' => $this->cdn(),
            'usage' => $this->usage(),
            'fallback' => $this->fallback()
        ];
    }

    function id(): string
    {
        return $this->id;
    }

    function provider(): string
    {
        return 'google_webfonts';
    }

    /**
     * @inheritDoc
     */
    function variants(): array
    {
        return $this->variants;
    }

    /**
     * @inheritDoc
     */
    function subsets(): array
    {
        return $this->subsets;
    }

    function category(): string
    {
        return $this->category;
    }

    function version(): string
    {
        return $this->version;
    }

    function defSubset(): string
    {
        return $this->defSubset;
    }

    function defVariant(): string
    {
        return $this->defVariant;
    }

    function cdn(): string
    {
        // Format examples:
        // https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100;1,300;1,400;1,500;1,700&display=swap
        // https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100;1,100;0,300;1,300;0,400;1,400;0,500;1,500;0,700;1,700;0,900;1,900;&display=swap
        // https://fonts.googleapis.com/css2?family=Roboto:italic&subset=greek

        $loaderURLArr = [
            "https://fonts.googleapis.com/css2?",
            "family=" . str_replace(" ", "+", $this->family()),
            $this->hasItalic() ? ":ital," : "",
        ];

        // 0,100;
        $parts = [];
        foreach ($this->variants() as $v) {
            $p = [];

            if ($this->hasNormal() && $this->hasItalic()) {
                $p[] = $this->parseFontStyle($v) === 'italic' ? 1 : 0;
                $p[] = ",";
            }


            $p[] = $this->parseFontWeight($v);
            $p[] = ";";
            $parts[] = implode("", $p);
        }

        if (count($parts) > 1) {
            $loaderURLArr[] = $this->hasItalic() ? "wght@" : ":wght@";
            // order is important!!
            sort($parts);
            $parts[count($parts) - 1] = rtrim($parts[count($parts) - 1], ";");
            $loaderURLArr = array_merge($loaderURLArr, $parts);
        }
        $loaderURLArr[] = "&display=swap";
        return implode("", $loaderURLArr);
    }

    private function parseFontStyle($variantId)
    {
        if ($variantId === 'regular' || preg_match('/^\d*$/', $variantId)) {
            return 'normal';
        }
        if ($variantId === 'italic' || preg_match('/^\d*italic$/', $variantId)) {
            return 'italic';
        }
        throw new WebfontsException('Unknown $variantId: ' . $variantId);
    }

    private function parseFontWeight($variantId)
    {
        if ($variantId === 'regular' || $variantId === 'italic') {
            return 400;
        }

        if (preg_match('/^(\d{2,4})\w*$/', $variantId, $m)) {
            return $m[1];
        }
        throw new WebfontsException('Unknown $variantId: ' . $variantId);
    }

    function family(): string
    {
        return $this->family;
    }

    private function fallback()
    {
        $cat = $this->category();
        if ($cat === 'handwriting' || $cat === 'display') {
            return "cursive";
        }
        if ($cat === 'serif') {
            return "serif";
        }
        if ($cat === 'monospace') {
            return "monospace";
        }
        return 'sans-serif';
    }

    private function usage()
    {
        $fallback = $this->fallback();
        $r = [];
        foreach ($this->variants() as $variant) {
            $r[$variant] = [
                'family' => '\'' . $this->family() . '\', ' . $fallback,
                'style' => $this->parseFontStyle($variant),
                'weight' => $this->parseFontWeight($variant),
            ];
        }
        return $r;
    }

}
