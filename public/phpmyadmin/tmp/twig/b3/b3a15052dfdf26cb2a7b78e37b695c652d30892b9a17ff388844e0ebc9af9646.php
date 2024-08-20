<?php

use Twig\Environment;
use Twig\Source;

/* display/results/page_selector.twig */
class __TwigTemplate_acdee9342dd0e9a007ab3b256ce109618e013d17bf50217168708ced34eeecf4 extends \Twig\Template
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
        echo "<td>
  <form action=\"";
        // line 2
        echo PhpMyAdmin\Url::getFromRoute("/sql");
        echo "\" method=\"post\">
    ";
        // line 3
        echo PhpMyAdmin\Url::getHiddenInputs(($context["url_params"] ?? null));
        echo "
    ";
        // line 4
        echo($context["page_selector"] ?? null);
        echo "
  </form>
</td>
";
    }

    public function getTemplateName()
    {
        return "display/results/page_selector.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  48 => 4,  44 => 3,  40 => 2,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/page_selector.twig", "/home/cbdev/crater/public/phpmyadmin/templates/display/results/page_selector.twig");
    }
}
