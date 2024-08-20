<?php

use Twig\Environment;
use Twig\Source;

/* sql/relational_column_dropdown.twig */
class __TwigTemplate_b393ceecad19d7a61928f77e205145b7d384735eee7dc9894a2b65da1b212696 extends \Twig\Template
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
        echo "<span class=\"curr_value\">";
        echo twig_escape_filter($this->env, ($context["current_value"] ?? null), "html", null, true);
        echo "</span>
<a href=\"";
        // line 2
        echo PhpMyAdmin\Url::getFromRoute("/browse-foreigners");
        echo "\" data-post=\"";
        echo PhpMyAdmin\Url::getCommon(($context["params"] ?? null), "", false);
        echo "\" class=\"ajax browse_foreign\">
  ";
        // line 3
        echo _gettext("Browse foreign values");
        // line 4
        echo "</a>
";
    }

    public function getTemplateName()
    {
        return "sql/relational_column_dropdown.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  50 => 4,  48 => 3,  42 => 2,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "sql/relational_column_dropdown.twig", "/home/cbdev/crater/public/phpmyadmin/templates/sql/relational_column_dropdown.twig");
    }
}
