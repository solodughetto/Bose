<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GenusNote;
use AppBundle\Service\MarkdownTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Genus;

class GenusController extends Controller
{
    /**
     * @Route("/admin/genus", name="admin_genus_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $genuses = $em->getRepository('AppBundle:Genus')
            ->findAllPublishedOrderedByRecentlyActive();

        return $this->render('admin/genus/list.html.twig', [
            'genuses' => $genuses
    ]);
    }


    /**
     * @Route("/admin/genus/new")
     */
    public function newAction()
    {
        return new Response('<html><body>genus Created !</body></html>');
    }

    /**
     * @Route("/admin/genus/{name}", name="genus_show")
     */
    public function showAction($name)
    {
        $em = $this->getDoctrine()->getManager();

        $genus = $em->getRepository('AppBundle:Genus')
            ->findOneBy(['name' => $name]);

        // todo - add the caching back later
        /*
         * $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
         * $key = md5($funFact);
         * if ($cache->contains($key)) {
         *      $funFact = $cache->fetch($key);
         * } else {
         *      sleep(1); // fake how slow this could be
         *      $funFact = $this->get('markdown.parser')
         *          ->transform($funFact);
         *      $cache->save($key, $funFact);
         * }
         */

        if (!$genus) {
            throw $this->createNotFoundException('Genus not found');
        }

        $MarkdownTransformer = $this->get('app.markdown_transformer');

        $funFact = $MarkdownTransformer->parse($genus->getFunFact());

//        $recentNotes = $genus->getNotes()
//            ->filter(function(GenusNote $note) {
//                return $note->getCreatedAt() > new \DateTime('-3 months');
//            });

        $recentNotes = $em->getRepository('AppBundle:GenusNote')
            ->findAllRecentNotesForGenus($genus);

        return $this->render('admin/genus/show.html.twig', [
            'genus' => $genus,
            'recentNoteCount' => count($recentNotes),
            'funFact' => $funFact
        ]);
    }

    /**
     * @Route("/admin/genus/{name}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus)
    {
        $notes = [];
//        $notes = [
//            ['id' => 1, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
//            ['id' => 2, 'username' => 'AquaWeaver', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
//            ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
//        ];

        foreach ($genus->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/'.$note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y')
            ];
        }
        $data = [
            'notes' => $notes
        ];

        return new JsonResponse($data);
    }
}
