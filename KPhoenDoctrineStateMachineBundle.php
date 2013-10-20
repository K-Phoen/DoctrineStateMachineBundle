<?php

namespace KPhoen\DoctrineStateMachineBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use KPhoen\DoctrineStateMachineBundle\DependencyInjection\Compiler\LoaderCompilerPass;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class KPhoenDoctrineStateMachineBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LoaderCompilerPass());
    }
}
