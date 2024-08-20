<?php

use Twig\Environment;
use Twig\Source;

/* display/results/empty_display.twig */
class __TwigTemplate_5fa1e7a0be585fbfcccd01fe4c58beda558259424c576399ccd00d6372775a47 extends \Twig\Template
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
        echo "<td ";
        echo twig_escape_filter($this->env, ($context["align"] ?? null), "html", null, true);
        echo " class=\"";
        echo twig_escape_filter($this->env, ($context["classes"] ?? null), "html", null, true);
        echo "\"></td>
";
    }

    public function getTemplateName()
    {
        return "display/results/empty_display.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/empty_display.twig", "/home/cbdev/crater/public/phpmyadmin/templates/display/results/empty_display.twig");
    }
}
