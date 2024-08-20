<?php

use Twig\Environment;
use Twig\Source;

/* config/form_display/group_header.twig */
class __TwigTemplate_4ce046fd592430d8ead0c62373823a011c5352e8a7f9171f40190a6263c54b8f extends \Twig\Template
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
        echo "<tr class=\"group-header group-header-";
        echo twig_escape_filter($this->env, ($context["group"] ?? null), "html", null, true);
        echo "\">
    <th colspan=\"";
        // line 2
        echo twig_escape_filter($this->env, ($context["colspan"] ?? null), "html", null, true);
        echo "\">
        ";
        // line 3
        echo twig_escape_filter($this->env, ($context["header_text"] ?? null), "html", null, true);
        echo "
    </th>
</tr>
";
    }

    public function getTemplateName()
    {
        return "config/form_display/group_header.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  46 => 3,  42 => 2,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "config/form_display/group_header.twig", "/home/cbdev/crater/public/phpmyadmin/templates/config/form_display/group_header.twig");
    }
}
