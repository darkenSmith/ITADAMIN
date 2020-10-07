<?php

namespace App\Controllers;

use App\Helpers\TemplateInterface;

/**
 * Class AbstractController
 * @package App\Controllers
 */
class AbstractController
{
    /**
     * @var TemplateInterface $template
     */
    public $template;

    /**
     * AbstractController constructor.
     * @param TemplateInterface $template
     */
    public function __construct(TemplateInterface $template)
    {
        $this->template = $template;
    }
}
