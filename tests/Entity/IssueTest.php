<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Issue;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IssueTest extends KernelTestCase
{
    public function testEntity(): void
    {
        $issue = new Issue();
        $comment = new Comment();
        $user = new User();

        $issue->setTitle('title');
        $issue->setDescription('description');
        $issue->setPriority('low');
        $issue->setStatus('new');
        $issue->setAuthor($user);
        self::assertEquals('title', $issue->getTitle());
        self::assertEquals('description', $issue->getDescription());
        self::assertEquals('low', $issue->getPriority());
        self::assertEquals('new', $issue->getStatus());
        self::assertEquals($user, $issue->getAuthor());

        $issue->addComment($comment);
        self::assertCount(1, $issue->getComments());

        $issue->removeComment($comment);
        self::assertEmpty($issue->getComments());
    }

    public function testValidPriority() : void
    {
        $user = new User();
        $issue = (new Issue())
            ->setTitle('title')
            ->setDescription('description')
            ->setPriority('low')
            ->setStatus('new')
            ->setAuthor($user);
        self::bootKernel();
        $error = static::getContainer()->get('validator')->validate($issue);
        self::assertCount(0, $error);
    }

    public function testInvalidPriority() : void
    {
        $user = new User();
        $issue = (new Issue())
            ->setTitle('title')
            ->setDescription('description')
            ->setPriority('high')
            ->setStatus('new')
            ->setAuthor($user);
        self::bootKernel();
        $error = static::getContainer()->get('validator')->validate($issue);
        self::assertCount(0, $error);
    }
}
