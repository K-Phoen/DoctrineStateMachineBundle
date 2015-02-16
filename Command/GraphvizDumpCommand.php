<?php

namespace KPhoen\DoctrineStateMachineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use KPhoen\DoctrineStateMachineBundle\Visualisation\Graphviz;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class GraphvizDumpCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('state-machine:dump:graphviz')
            ->setDescription('Generate a graphiz dump of a state machine')
            ->addArgument('state-machine', InputArgument::REQUIRED, 'The state machine to dump')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stateMachine = $this->getStateMachine($input->getArgument('state-machine'));
        $visualisation = new Graphviz();

        $output->write($visualisation->render($stateMachine));
    }

    private function getStateMachine($name)
    {
        $factory = $this->getContainer()->get('kphoen.state_machine.factory');

        return $factory->getNamed($name);
    }
}
