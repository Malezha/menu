<?php
namespace Malezha\Menu\Contracts;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * Interface ComparativeUrl
 * @package Malezha\Menu\Contracts
 */
interface ComparativeUrl
{
    /**
     * ComparativeUrl constructor.
     * @param UrlGenerator $generator
     * @param array $skippedPaths
     */
    public function __construct(UrlGenerator $generator, $skippedPaths = []);

    /**
     * @param string $url
     * @return bool
     */
    public function isEquals($url);
}