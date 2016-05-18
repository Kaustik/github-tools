<?php

namespace Kaustik\AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpsertImageLinkToPullRequestDescription extends Command
{
    protected function configure()
    {
        $this
            ->setName('github-tools:upsertimageinpullrequest')
            ->addArgument(
                'pullrequestnumber',
                InputArgument::REQUIRED,
                'name of actual branch'
            )
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'github token'
            )
            ->addArgument(
                'image-url',
                InputArgument::REQUIRED,
                'github token'
            )
            ->setDescription(
                'set image link'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pullrequestnumber = $input->getArgument('pullrequestnumber');
        $token = $input->getArgument('token');
        $imageUrl = $input->getArgument('image-url');
        $client = new \GitHubClient();
        $owner = 'kaustik';
        $repo = 'aiai';
        $client->setAuthType(\GitHubClient::GITHUB_AUTH_TYPE_OAUTH_BASIC);
        $client->setOauthKey($token);
        $client->setDebug(false);
        $pullRequest = $client->pulls->getSinglePullRequest($owner, $repo, $pullrequestnumber);
        $body = self::getNewBody($pullRequest->getBody(), $imageUrl);
        try {
            $client->pulls->updatePullRequest(
                $owner,
                $repo,
                $pullrequestnumber,
                null,
                null,
                $body
            );
        } catch (\GitHubClientException $e) {
            echo $e->getMessage();
            echo $e->getCode();
        }
    }

    public static function getNewBody($body, $imageUrl)
    {
        if (strstr($body, '[uml]')) {
            $newBody = preg_replace('/!\[uml]\((.*)\)/', "![uml](${imageUrl})", $body);
        } else {
            $newBody = "${body}\n![uml](${imageUrl})";
        }

        return $newBody;
    }
}
