<?php

use Twig\Environment;
use Twig\Source;

/* config/form_display/tabs_bottom.twig */
class __TwigTemplate_b24944ae8918f57deac711ca33ba9cb571425a285cc9fc19557a2430285c812a extends \Twig\Template
{
    private $source;

    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "config/form_display/tabs_bottom.twig";
    }

    public function getDebugInfo()
    {
        return  [  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "config/form_display/tabs_bottom.twig", "/home/cbdev/crater/public/phpmyadmin/templates/config/form_display/tabs_bottom.twig");
    }
}
