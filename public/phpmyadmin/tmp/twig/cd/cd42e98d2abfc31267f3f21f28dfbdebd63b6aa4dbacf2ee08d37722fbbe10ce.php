<?php

use Twig\Environment;
use Twig\Source;

/* login/footer.twig */
class __TwigTemplate_4152d750075d6a094aad0b57e9205d112101ee5aa18d9c9e62e2d7ffd31df25b extends \Twig\Template
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
        // line 2
        if ((($context["check_timeout"] ?? null) == true)) {
            // line 3
            echo "    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "login/footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  42 => 3,  40 => 2,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "login/footer.twig", "/home/cbdev/crater/public/phpmyadmin/templates/login/footer.twig");
    }
}
