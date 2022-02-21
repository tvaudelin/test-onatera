<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Issue;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testEntity(): void
    {
        $comment = new Comment();
        $user = new User();
        $issue = new Issue();

        $comment->setAuthor($user);
        $comment->setContent('content');
        $comment->setIssue($issue);
        self::assertEquals($user, $comment->getAuthor());
        self::assertEquals('content', $comment->getContent());
        self::assertEquals($issue, $comment->getIssue());
    }
}
