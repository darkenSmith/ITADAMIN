<?php

namespace App\Helpers;

/**
 * Interface TemplateInterface
 * @package App\Helpers
 */
interface TemplateInterface
{

    /**
     * @param string $template
     * @param array $vars
     */
    public function view(string $template, array $vars = []): void;
}
