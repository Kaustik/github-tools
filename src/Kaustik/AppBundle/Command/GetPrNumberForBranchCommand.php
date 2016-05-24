<?php

namespace Kaustik\AppBundle\Command;

use Exception;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetPrNumberForBranchCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('github-tools:getprnumberforbranch')
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
                'Get Pull request number from github for a speficic branch'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $input->getArgument('token');
        $currentBranch = $input->getArgument('branch');
        try {
            $pullrequest = self::getPullRequestForBranch($currentBranch, $token);
        } catch (Exception $exception) {
            echo $exception->getMessage()."\n";

            return 1;
        }
        $output->writeln($pullrequest->number);

        return 0;
    }

    /**
     * @param string $currentBranch
     * @param string $token
     *
     * @return stdClass
     *
     * @throws Exception
     */
    public static function getPullRequestForBranch($currentBranch, $token)
    {
        $command = "curl -H 'Authorization: token $token' ".
            'https://api.github.com/repos/kaustik/aiai/pulls';
        $jsonResult = `$command`;
        $pullRequestList = json_decode($jsonResult);

        if ($pullRequestList instanceof stdClass) {
            throw new Exception("Github error: {$pullRequestList->message}");
        }

        foreach ($pullRequestList as $pullRequest) {
            $pullRequestBranch = $pullRequest->head->ref;
            if ($pullRequestBranch == $currentBranch) {
                return $pullRequest;
            }
        }

        throw new Exception('Pull request not found for branch '.$currentBranch);
    }
}
