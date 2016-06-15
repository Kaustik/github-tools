<?php

namespace Kaustik\AppBundle\Command;

use GitHubClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HasUmlInTitleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('github-tools:hasumlintitle')
            ->addArgument(
                'pullrequestnumber',
                InputArgument::REQUIRED,
                'pull request number'
            )
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'github token'
            )
            ->setDescription(
                'return 0 if #uml exist in pull request title, 1 otherwise'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $input->getArgument('token');
        $pullrequestnumber = $input->getArgument('pullrequestnumber');
        $client = new GitHubClient();
        $owner = 'kaustik';
        $repo = 'aiai';
        $client->setAuthType(GitHubClient::GITHUB_AUTH_TYPE_OAUTH_BASIC);
        $client->setOauthKey($token);
        $client->setDebug(false);
        $pullRequest = $client->pulls->getSinglePullRequest($owner, $repo, $pullrequestnumber);

        if (strstr($pullRequest->getTitle(), '#uml')) {
            return 0;
        } else {
            return 1;
        }
    }
}
