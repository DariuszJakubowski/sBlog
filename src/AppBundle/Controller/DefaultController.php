<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
//use AppBundle\Entity\User;
use AppBundle\Form\Type\CommentType;
// do akcji tempAction()
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{

    /**
     * @Route("/{page}", defaults={"page"=1}, name="homepage")
     */
    public function indexAction(Request $request, $page)
    {
        $posts = $this->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->createQueryBuilder('p')
            ->getQuery();


        $paginator = $this->get('knp_paginator');
        try {
            $pagination = $paginator->paginate(
                $posts, 
                $request->query->get('page', $page),
                10);
            if (count($pagination) < 1) {
                throw $this->createNotFoundException('404 nie ma takiej strony');
            }
        } catch (Exception $exception) {

            return new Respone('<body>' . $exception->getMessage() . '</body>');
        }



        return $this->render('default/index.html.twig', array(
                'posts' => $pagination));
    }

    /**
     * 
     * @param Post $post
     * @Route("/article/{id}")
     */
    public function showAction(Post $post, Request $request)
    {
        $comment = new Comment();
        $comment->setPost($post);

        $user = $this->getUser();
        $comment->setUser($user);


        //$commentForm = $this->createForm(new CommentType); //s=>2.8 depreciated / s=>3 removed
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Komentarz dodany!');

            return $this->redirectToRoute('app_default_show', ['id' => $post->getId()]);
        }


        return $this->render('AppBundle:Default:show.html.twig', [
                'post' => $post,
                'form' => $commentForm->createView()
        ]);
    }

    // test entity menager
    /**
     * 
     * @param Post $post
     * @Route("/temp/{id}")
     * @ParamConverter("post", class="AppBundle:Post") 
     */
    public function tempAction(Post $post)
    {

        return new Response("<body>$post</body>");
    }
}
