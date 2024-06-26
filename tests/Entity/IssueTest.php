<?php
namespace App\Tests\Service;

use App\Entity\Issue;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Unit test of Issue entity
 */
class IssueTest extends KernelTestCase
{

    public function testIssueGetStatusAsString(): void
    {
        self::bootKernel();
        $issue = new Issue();
        $issue->setStatus(Issue::STATUS_NEW);
        $this->assertEquals("Nouveau", $issue->getStatusAsString());
        $issue->setStatus(Issue::STATUS_OPEN);
        $this->assertEquals("Ouvert", $issue->getStatusAsString());
        $issue->setStatus(Issue::STATUS_CLOSED);
        $this->assertEquals("Terminé", $issue->getStatusAsString());
        $issue->setStatus(99);
        $this->assertEquals("Inconnu", $issue->getStatusAsString());
    }

    public function testIssueSetStatusFromString(): void
    {
        self::bootKernel();
        $issue = new Issue();
        $issue->setStatusFromString("Nouveau");
        $this->assertEquals(Issue::STATUS_NEW, $issue->getStatus());
        $issue->setStatusFromString("Ouvert");
        $this->assertEquals(Issue::STATUS_OPEN, $issue->getStatus());
        $issue->setStatusFromString("Terminé");
        $this->assertEquals(Issue::STATUS_CLOSED, $issue->getStatus());
        $issue->setStatusFromString("Random");
        $this->assertEquals(99, $issue->getStatus());
    }
}
