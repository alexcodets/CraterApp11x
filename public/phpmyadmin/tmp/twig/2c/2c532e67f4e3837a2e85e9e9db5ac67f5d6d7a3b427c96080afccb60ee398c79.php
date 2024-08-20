<?php

use Twig\Environment;
use Twig\Source;

/* sql/enum_column_dropdown.twig */
class __TwigTemplate_274da58d841b94ae676043dffb70cabfdc42a544ac8b4ef148d4e0695fcb7082 extends \Twig\Template
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
        echo "<select>
  <option value=\"\">&nbsp;</option>
  ";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["values"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["value"]) {
            // line 4
            echo "    <option value=\"";
            echo $context["value"];
            echo "\"";
            echo((twig_in_filter($context["value"], ($context["selected_values"] ?? null))) ? (" selected") : (""));
            echo ">";
            echo $context["value"];
            echo "</option>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 6
        echo "</select>
";
    }

    public function getTemplateName()
    {
        return "sql/enum_column_dropdown.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  58 => 6,  45 => 4,  41 => 3,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "sql/enum_column_dropdown.twig", "/home/cbdev/crater/public/phpmyadmin/templates/sql/enum_column_dropdown.twig");
    }
}
