<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Topic;
use App\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\File;

class NoteController extends AbstractController
{
    /**
     * @Route("/note/{id}", name="note_topic", defaults={"id"=3})
     * @param $id
     * @return Response
     */
    public function viewTopic($id)
    {

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("App:Topic");

        $topic = $repo->find($id);

        //If the topic id is not found throw an exception
        if ($topic == null) {
            throw $this->createNotFoundException("Page " . $id . " not found");
        }

        if (count($topic->getNotes()) > 0) {
            $notes = $topic->getNotes();
            $back = $topic->getParent()->getId();
            return $this->render('note/note.html.twig', [
                'notes' => $notes,
                'id' => $id,
                'back' => $back,
            ]);
        }

        //Get direct children
        $topics = $repo->getChildren($topic, true);

        //If is not top level topic then get id of parent
        if ($topic->getLvl() !== 0) {
            $parent = $topic->getParent();
            $backId = $parent->getID();
        } else {
            $backId = -1;
        }

        $title = $topic->getTitle();

        //Render
        return $this->render('note/index.html.twig', [
            'topics' => $topics,
            'backId' => $backId,
            'id' => $id,
            'title' => $title,
        ]);
    }

    /**
     * @Route("/note_create/{id}", name="note_topic_create")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function createTopic($id, Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository("App:Topic");
        $parent = $repo->find($id);

        //init new topic
        $topic = new Topic();
        $topic->setParent($parent);

        //Setup form
        $form = $this->createFormBuilder($topic)
            ->add('title', TextType::class, ['attr' => ['autocomplete' => null]])
            ->add('submit', SubmitType::class, ['label' => 'Create topic'])
            ->add('enterNew', CheckboxType::class, [
                'mapped' => false,
                'label' => "Open new topic after creation",
                'value' => true,
                'attr' => ['checked' => 'checked'],
                'required' => false,
            ])
            ->getForm();

        //process form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $topic = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($topic);
            $entityManager->flush();
            $topicId = $topic->getId();
            if (!$topicId || !$form['enterNew']->getData()) {
                $topicId = $id;
            }
            return $this->redirectToRoute('note_topic', ['id' => $topicId]);
        }

        return $this->render('note/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/note/createNote/{id}/{type}", name="note_create")
     * @param $id
     * @param $type
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createNote($id, $type, Request $request, FileUploader $fileUploader)
    {
        $note = new Note();
        $formTemplate = "note/create.html.twig";

        switch ($type) {
            case "img":
            {
                $form = $this->createFormBuilder($note)
                    ->add('title', TextType::class, ['required' => true, 'attr' => ['autocomplete' => null]])
                    ->add('content', FileType::class, [
                        'required' => true,
                        'mapped' => false,
                        'constraints' => [
                            new File([
                                'maxSize' => '1024k',
                                'mimeTypes' => [
                                    'image/png',
                                    'image/jpeg',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid image file',
                            ])
                        ]
                    ])
                    ->add('submit', SubmitType::class, ['label' => 'Add image'])
                    ->getForm();
                echo $type;
                break;
            }
            case "formula":
            {
                $formTemplate = "note/formula_input.html.twig";
            }
            default:
            {
                $form = $this->createFormBuilder($note)
                    ->add('title', TextType::class, ['required' => false, 'attr' => ['autocomplete' => null]])
                    ->add('content', TextType::class, ['required' => true, 'attr' => ['autocomplete' => null]])
                    ->add('submit', SubmitType::class, ['label' => 'Add content'])
                    ->getForm();
                break;
            }

        }


        $note->setType($type);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $note = $form->getData();
            if ($type == "img") {
                $imageFile = $form['content']->getData();
                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $note->setContent($imageFileName);
                }

            }
            $entityManager = $this->getDoctrine()->getManager();
            if ($type == "intro") {
                $topic = new Topic();
                $topic->setParent($this->getDoctrine()->getManager()->getRepository("App:Topic")->find($id));
                $topic->setTitle($note->getTitle());
                $entityManager->persist($topic);
            } else {
                $topic = $this->getDoctrine()->getManager()->getRepository("App:Topic")->find($id);
            }

            $note->setTopic($topic);

            $entityManager->persist($note);
            $entityManager->flush();
            return $this->redirectToRoute('note_topic', ['id' => $id]);
        }
        return $this->render($formTemplate, ['form' => $form->createView()]);
    }

    /**
     * @Route("note/remove_topic/{id}",name="note_remove_topic")
     * @param $id
     * @return RedirectResponse|Response
     */
    public function removeTopic($id, Request $request)
    {
        if ($request->query->get("confirm")) {
            $em = $this->getDoctrine()->getManager();
            $repo = $this->getDoctrine()->getRepository("App:Topic");
            $topic = $repo->find($id);
            $returnId = $topic->getParent()->getID();
            $repo->removeFromTree($topic);
            $em->flush();
            $em->clear();

            return $this->redirectToRoute('note_topic', [
                'id' => $returnId,
            ]);
        } else {
            return new Response("<html><body><a href='?confirm=true'>Click to remove topic and all subtopics and notes within it...</body></html>");
        }
    }

}
