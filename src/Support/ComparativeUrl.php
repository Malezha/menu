<?php
namespace Malezha\Menu\Support;

use Illuminate\Contracts\Routing\UrlGenerator;
use Malezha\Menu\Contracts\ComparativeUrl as ComparativeUrlContract;

class ComparativeUrl implements ComparativeUrlContract
{
    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var array
     */
    protected $skippedPaths = [];

    /**
     * @var array
     */
    protected $components = [
        'host', 'path', 'query',
    ];

    /**
     * @var array
     */
    protected $parsedCurrentUrl;

    /**
     * @inheritDoc
     */
    public function __construct(UrlGenerator $generator, $skippedPaths = [])
    {
        $this->urlGenerator = $generator;
        $this->skippedPaths = $skippedPaths;
        $this->parsedCurrentUrl = parse_url($generator->current());
    }

    /**
     * @inheritDoc
     */
    public function isEquals($url)
    {
        if (in_array($url, $this->skippedPaths)) {
            return false;
        }

        $parsedUrl = parse_url($this->buildUrl($url));

        $counterMin = $this->getMinCounterValue($parsedUrl);
        $counter = 0;

        foreach ($this->components as $component) {
            if ($this->checkComponent($component, $parsedUrl)) {
                $counter++;
            }
        }

        if ($counter >= $counterMin) {
            return true;
        }

        return false;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function buildUrl($url)
    {
        return $this->urlGenerator->to($url);
    }

    /**
     * @param string $component
     * @param array $parsedUrl
     * @return bool
     */
    protected function checkComponent($component, $parsedUrl)
    {
        if (array_key_exists($component, $this->parsedCurrentUrl) &&
            array_key_exists($component, $parsedUrl) &&
            $parsedUrl[$component] === $this->parsedCurrentUrl[$component]
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param array $parsedUrl
     * @return int
     */
    protected function getMinCounterValue($parsedUrl)
    {
        return max(count(array_intersect($this->components, array_keys($parsedUrl))),
            count(array_intersect($this->components, array_keys($this->parsedCurrentUrl))));
    }
}