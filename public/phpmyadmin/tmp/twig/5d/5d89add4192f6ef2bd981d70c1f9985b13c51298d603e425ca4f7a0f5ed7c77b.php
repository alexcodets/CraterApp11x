<?php

use Twig\Environment;
use Twig\Source;

/* database/import/index.twig */
class __TwigTemplate_691e66b9533e0f468edef6f441a3a1817b5ffb80bfd86d368964e189a206df3c extends \Twig\Template
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
        $this->parent = $this->loadTemplate("import.twig", "database/import/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo twig_escape_filter($this->env, twig_sprintf(_gettext("Importing into the database \"%s\""), ($context["db"] ?? null)), "html", null, true);
    }

    public function getTemplateName()
    {
        return "database/import/index.twig";
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
        return new Source("", "database/import/index.twig", "/home/cbdev/crater/public/phpmyadmin/templates/database/import/index.twig");
    }
}
