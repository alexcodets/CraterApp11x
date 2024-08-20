<?php

use Twig\Environment;
use Twig\Source;

/* database/structure/collation_definition.twig */
class __TwigTemplate_a2f5a0fbef0176b029be4ecf9ccc1726ef013f28ceee075e1da1a0df1f8b3128 extends \Twig\Template
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
        echo "<dfn title=\"";
        echo twig_escape_filter($this->env, ($context["valueTitle"] ?? null), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
        echo "</dfn>
";
    }

    public function getTemplateName()
    {
        return "database/structure/collation_definition.twig";
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
        return new Source("", "database/structure/collation_definition.twig", "/home/cbdev/crater/public/phpmyadmin/templates/database/structure/collation_definition.twig");
    }
}
