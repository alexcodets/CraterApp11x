<?php

use Twig\Environment;
use Twig\Source;

/* table/search/column_comparison_operators.twig */
class __TwigTemplate_1f52adfbf1e55876eb12d792e4e8bf738950748ad72fd324c342050303d032de extends \Twig\Template
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
        echo "<select class=\"column-operator\" id=\"ColumnOperator";
        echo twig_escape_filter($this->env, ($context["search_index"] ?? null), "html", null, true);
        echo "\" name=\"criteriaColumnOperators[";
        echo twig_escape_filter($this->env, ($context["search_index"] ?? null), "html", null, true);
        echo "]\">
    ";
        // line 2
        echo($context["type_operators"] ?? null);
        echo "
</select>
";
    }

    public function getTemplateName()
    {
        return "table/search/column_comparison_operators.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  44 => 2,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "table/search/column_comparison_operators.twig", "/home/cbdev/crater/public/phpmyadmin/templates/table/search/column_comparison_operators.twig");
    }
}
