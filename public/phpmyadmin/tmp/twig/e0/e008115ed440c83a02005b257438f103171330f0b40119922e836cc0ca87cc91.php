<?php

use Twig\Environment;
use Twig\Source;

/* javascript/display.twig */
class __TwigTemplate_948bf6bba82af678fb1f739def037583942e7d8d48441fb6f424a4455bae162b extends \Twig\Template
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
        echo "<script type=\"text/javascript\">
    if (typeof configInlineParams === 'undefined' || !Array.isArray(configInlineParams)) {
        configInlineParams = [];
    }
    configInlineParams.push(function () {
        ";
        // line 6
        echo twig_join_filter(($context["js_array"] ?? null), ";
");
        echo ";
    });
    if (typeof configScriptLoaded !== 'undefined' && configInlineParams) {
        loadInlineConfig();
    }
</script>
";
    }

    public function getTemplateName()
    {
        return "javascript/display.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  44 => 6,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "javascript/display.twig", "/home/cbdev/crater/public/phpmyadmin/templates/javascript/display.twig");
    }
}
