<?php

namespace Kaustik\AppBundle\Command;

use Exception;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetPrStageForBranchCommand extends Command
{
    /**
     * @var array
     */
    private $availableStages;

    public function __construct()
    {
        // must be in this order. otherwise code find #pr1 instead of #pr10, for example
        $this->availableStages = [
            '#pr10', '#pr11', '#pr12', '#pr13', '#pr14', '#pr15, #pr16, #pr17, #pr18, #pr19, #pr20',
            '#pr1', '#pr2', '#pr3', '#pr4', '#pr5', '#pr6', '#pr7', '#pr8', '#pr9', '#pr10',
            '#staging', '#prstordb',
        ];
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('github-tools:getprstageforbranch')
            ->addArgument(
                'branch',
                InputArgument::REQUIRED,
                'name of actual branch'
            )
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'github token'
            )
            ->setDescription(
                'Get Pull request stage from github for a speficic branch, or emptry string if none exist'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentBranch = $input->getArgument('branch');
        $token = $input->getArgument('token');
        try {
            $pullRequest = GetPrNumberForBranchCommand::getPullRequestForBranch($currentBranch, $token);
        } catch (Exception $exception) {
            echo $exception->getMessage()."\n";

            return 1;
        }

        $stage = $this->getStageOrNullFromPullrequest($pullRequest);
        if ($stage) {
            $output->writeln($stage);

            return 0;
        }

        return 1;
    }

    private function getStageOrNullFromPullrequest(stdClass $pullRequest)
    {
        foreach ($this->availableStages as $stage) {
            if (strripos($pullRequest->title, $stage) !== false) {
                $stageWithoutHash = substr($stage, 1);

                return $stageWithoutHash;
            }
        }
    }
}
