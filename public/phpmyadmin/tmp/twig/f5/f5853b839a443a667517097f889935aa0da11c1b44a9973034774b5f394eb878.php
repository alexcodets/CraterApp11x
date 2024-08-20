<?php

use Twig\Environment;
use Twig\Source;

/* table/import/index.twig */
class __TwigTemplate_2834100f79b9175593532797d944becf960224d24da7d77734019f5f54ef383c extends \Twig\Template
{
    private $source;

    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "import.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("import.twig", "table/import/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo twig_escape_filter($this->env, twig_sprintf(_gettext("Importing into the table \"%s\""), ($context["table"] ?? null)), "html", null, true);
    }

    public function getTemplateName()
    {
        return "table/import/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  46 => 3,  35 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "table/import/index.twig", "/home/cbdev/crater/public/phpmyadmin/templates/table/import/index.twig");
    }
}
