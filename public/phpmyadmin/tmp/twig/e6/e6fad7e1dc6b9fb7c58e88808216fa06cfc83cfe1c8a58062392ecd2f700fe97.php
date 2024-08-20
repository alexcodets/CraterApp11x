<?php

use Twig\Environment;
use Twig\Source;

/* message.twig */
class __TwigTemplate_28b3c10d592bec1f4693a668a514aaec564f8bc02d3d2e5e0a52ebbd5ff7a877 extends \Twig\Template
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
        echo "<div class=\"alert alert-";
        echo twig_escape_filter($this->env, ($context["context"] ?? null), "html", null, true);
        echo "\" role=\"alert\">
  ";
        // line 2
        echo($context["message"] ?? null);
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "message.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  42 => 2,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "message.twig", "/home/cbdev/crater/public/phpmyadmin/templates/message.twig");
    }
}
