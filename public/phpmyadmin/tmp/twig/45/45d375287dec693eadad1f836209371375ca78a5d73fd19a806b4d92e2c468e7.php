<?php

use Twig\Environment;
use Twig\Source;

/* javascript/redirect.twig */
class __TwigTemplate_ea56539792b4b85bc3785f67d216755728e3dd25715d08298ed0737fec236ef7 extends \Twig\Template
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
        echo "<script type='text/javascript'>
    window.onload = function () {
        window.location = '";
        // line 3
        echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
        echo "';
    };
</script>
";
    }

    public function getTemplateName()
    {
        return "javascript/redirect.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  41 => 3,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "javascript/redirect.twig", "/home/cbdev/crater/public/phpmyadmin/templates/javascript/redirect.twig");
    }
}
