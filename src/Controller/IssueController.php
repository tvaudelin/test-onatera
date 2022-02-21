<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Entity\User;
use App\Form\Type\IssueType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IssueController
 *
 * @author Thierry Vaudelin <tvaudelin@gmail.com>
 */
class IssueController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // This controller gathers all the issues and render them in the index template
    #[Route('/', name: 'issue_index')]
    public function index(): Response
    {
        $issues = $this->entityManager->getRepository(Issue::class)
            ->findAll();

        foreach ($issues as $issue) {
            $issue->setStatus($this->translate_status($issue->getStatus()));
            $issue->setPriority($this->translate_priority($issue->getPriority()));
        }

        return $this->render('issue/index.html.twig', [
            'issues' => $issues
        ]);
    }


    // This controller has the following purposes :
    // - creates a new form for new issues (based on issueType)
    // - validates the inputs based on constraints defined on each property of the Issue class
    // - assign random author out of the three already created in the ddb
    // - save the new issue in the ddb
    // - once finished, it redirects to the issue_index page
    /**
     * @throws Exception
     */
    #[Route('/new_issue', name: 'issue_new')]
    public function new(Request $request): Response
    {
        $issue = new Issue();

        $status_list = [
            'nouveau' => 'new'
        ];

        $form = $this->createForm(IssueType::class, $issue, [
            'status_list' => $status_list
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $issue = $form->getData();

            // for time's sake, we assign random author to the issue
            $author = $this->entityManager->getRepository(User::class)
                ->findOneBy([
                    'id' => random_int(1, 3)
                ]);
            $issue->setAuthor($author);

            $this->entityManager->persist($issue);
            $this->entityManager->flush();

            return $this->redirectToRoute('issue_index');
        }

        return $this->renderForm('issue/new.html.twig', [
            'issueForm' => $form
        ]);
    }


    // This controller has the following purposes :
    // - edit an issue
    // - finds the issue based on the id passed as route parameter
    // - select the relevant statuses and creates a new form (based on issueType)
    // - validates the inputs based on constraints defined on each property of the Issue class
    // - save the new issue in the ddb, in which case, recreates the form with relevant statuses
    // - once finished, it renders the issue/edit.html.twig template
    #[Route('/edit_issue/{id}', name: 'issue_edit', requirements: ['id' => '\d+'])]
    public function edit(Int $id, Request $request): Response
    {
        $issue = $this->entityManager->getRepository(Issue::class)
            ->findOneBy([
                'id' => $id
            ]);

        $form = $this->create_form_with_proper_status($issue);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $issue = $form->getData();
            $this->entityManager->persist($issue);
            $this->entityManager->flush();

            $form = $this->create_form_with_proper_status($issue);
        }

        return $this->renderForm('issue/edit.html.twig', [
            'issueForm' => $form,
            'issue' => $issue
        ]);
    }

    // This method selects the relevant statuses available depending on the current status and creates
    // a form based on IssueType
    private function create_form_with_proper_status(Issue $issue): FormInterface
    {
        $status_list = match ($issue->getStatus()) {
            'new' => [
                'nouveau' => 'new',
                'en cours' => 'in progress',
                'pas de solution' => 'won\'t fix'
            ],
            'in progress' => [
                'en cours' => 'in progress',
                'résolu' => 'resolved',
                'pas de solution' => 'won\'t fix'
            ],
            default => $issue->getStatus() === 'resolved' ?
                ['résolu' => 'resolved'] :
                ['pas de solution' => 'won\'t fix'],
        };

        return $this->createForm(IssueType::class, $issue, [
            'status_list' => $status_list
        ]);
    }

    // Method to translate the status into French
    private function translate_status($status): string
    {
        return match ($status) {
            'new' => 'nouveau',
            'in progress' => 'en cours',
            'resolved' => 'résolu',
            'won\'t fix' => 'pas de solution'
        };
    }

    // Method to translate the priority into French
    private function translate_priority($priority): string
    {
        return match ($priority) {
            'low' => 'faible',
            'normal' => 'normal',
            'urgent' => 'urgent',
            'critical' => 'critique'
        };
    }
}
