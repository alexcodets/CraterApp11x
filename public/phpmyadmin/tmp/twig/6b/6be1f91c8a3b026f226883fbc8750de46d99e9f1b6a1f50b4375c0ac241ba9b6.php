<?php

use Twig\Environment;
use Twig\Source;

/* file_select_options.twig */
class __TwigTemplate_6a94ad1a65f3d8b54ea29167f7822869c792c2abc245c34752a78ea86e801dbc extends \Twig\Template
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
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["filesList"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
            // line 2
            echo "  <option value=\"";
            echo twig_escape_filter($this->env, $context["file"], "html", null, true);
            echo "\"";
            if (($context["file"] == ($context["active"] ?? null))) {
                echo " selected=\"selected\"";
            }
            echo ">
    ";
            // line 3
            echo twig_escape_filter($this->env, $context["file"], "html", null, true);
            echo "
  </option>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "file_select_options.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return  [  50 => 3,  41 => 2,  37 => 1,];
    }

    public function getSourceContext()
    {
        return new Source("", "file_select_options.twig", "/home/cbdev/crater/public/phpmyadmin/templates/file_select_options.twig");
    }
}
