<?php


namespace Yoda\EventBundle\Twig;

// Criando funcoes personalizadas para usar no template do twig
use Yoda\EventBundle\Util\DateUtil;

// exportar classe para usar as funcoes nos templates
class EventExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'event';
    }

    // pega os filtros passados e direciona para as funcoes
    public function getFilters ()
    {
        return array (
            new \Twig_SimpleFilter ('ago', array ( $this, 'calculateAgo' ))
        );
    }

    // usa a funcao dateutil criada no Util/DateUtil definida no config.yml
    public function calculateAgo(\DateTime $dt)
    {
        return DateUtil::ago($dt);
    }
}