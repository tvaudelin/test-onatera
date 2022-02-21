<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Issue;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testEntity(): void
    {
        $user = new User();
        $comment = new Comment();
        $issue = new Issue();

        $user->setFirstName('firstName');
        $user->setLastName('lastName');
        self::assertEquals('firstName', $user->getFirstName());
        self::assertEquals('lastName', $user->getLastName());

        $user->addComment($comment);
        $user->addIssue($issue);
        self::assertCount(1, $user->getComments());
        self::assertCount(1, $user->getIssues());

        $user->removeComment($comment);
        $user->removeIssue($issue);
        self::assertEmpty($user->getComments());
        self::assertEmpty($user->getIssues());
    }
}
