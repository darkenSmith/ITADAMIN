<?php

namespace App\Helpers;

/**
 * Class Template
 * @package App\Helpers
 */
class Template implements TemplateInterface
{
    private $templatePath = TEMPLATE_DIR;
    public const FILE_EXTENSION = 'php';

    /**
     * @param string $template
     * @param array $vars
     */
    public function view(string $template, array $vars = []): void
    {
        if ($template) {
            extract($vars);
            require_once(
                sprintf(
                    '%s%s.%s',
                    $this->templatePath,
                    $template,
                    self::FILE_EXTENSION
                )
            );
        }
    }
}
