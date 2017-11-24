<?php

namespace imc\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use imc\UserBundle\Entity\Group;
use imc\UserBundle\Form\Type\GroupType;
use imc\UserBundle\Form\Type\GroupFilterType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Group controller.
 *
 */
class GroupController extends Controller
{
    /**
     * Lists all Group entities.
     *
     */



    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new GroupFilterType());
        if (!is_null($response = $this->saveFilter($form, 'group', 'admin_groups'))) {
            return $response;
        }
        $qb = $em->getRepository('imcUserBundle:Group')->createQueryBuilder('g');
        $paginator = $this->filter($form, $qb, 'group');
                return $this->render('imcUserBundle:Group:index.html.twig', array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        ));
    }

    /**
     * Finds and displays a Group entity.
     *
     */
    public function showAction(Group $group)
    {
        $deleteForm = $this->createDeleteForm($group->getId(), 'admin_groups_delete');

        return $this->render('imcUserBundle:Group:show.html.twig', array(
            'group' => $group,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Group entity.
     *
     */
    public function newAction()
    {
        $group = new Group();
        $form = $this->createForm(new GroupType(), $group);

        return $this->render('imcUserBundle:Group:new.html.twig', array(
            'group' => $group,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Group entity.
     *
     */
    public function createAction(Request $request)
    {
        $group = new Group();
        $form = $this->createForm(new GroupType(), $group);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_groups_show', array('id' => $group->getId())));
        }

        return $this->render('imcUserBundle:Group:new.html.twig', array(
            'group' => $group,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Group entity.
     *
     */
    public function editAction(Group $group)
    {
        $editForm = $this->createForm(new GroupType(), $group, array(
            'action' => $this->generateUrl('admin_groups_update', array('id' => $group->getId())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($group->getId(), 'admin_groups_delete');

        return $this->render('imcUserBundle:Group:edit.html.twig', array(
            'group' => $group,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Group entity.
     *
     */
    public function updateAction(Group $group, Request $request)
    {
        $editForm = $this->createForm(new GroupType(), $group, array(
            'action' => $this->generateUrl('admin_groups_update', array('id' => $group->getId())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_groups_edit', array('id' => $group->getId())));
        }
        $deleteForm = $this->createDeleteForm($group->getId(), 'admin_groups_delete');

        return $this->render('imcUserBundle:Group:edit.html.twig', array(
            'group' => $group,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Save order.
     *
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('group', $field, $type);

        return $this->redirect($this->generateUrl('admin_groups'));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Save filters
     *
     * @param  FormInterface $form
     * @param  string        $name   route/entity name
     * @param  string        $route  route name, if different from entity name
     * @param  array         $params possible route parameters
     * @return Response
     */
    protected function saveFilter(FormInterface $form, $name, $route = null, array $params = null)
    {
        $request = $this->getRequest();
        $url = $this->generateUrl($route ?: $name, is_null($params) ? array() : $params);
        if ($request->query->has('submit-filter') && $form->handleRequest($request)->isValid()) {
            $request->getSession()->set('filter.' . $name, $request->query->get($form->getName()));

            return $this->redirect($url);
        } elseif ($request->query->has('reset-filter')) {
            $request->getSession()->set('filter.' . $name, null);

            return $this->redirect($url);
        }
    }

    /**
     * Filter form
     *
     * @param  FormInterface                                       $form
     * @param  QueryBuilder                                        $qb
     * @param  string                                              $name
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    protected function filter(FormInterface $form, QueryBuilder $qb, $name)
    {
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
            }
        }

        // possible sorting
        $this->addQueryBuilderSort($qb, $name);
        return $this->get('knp_paginator')->paginate($qb, $this->getRequest()->query->get('page', 1), 20);
    }

    /**
     * Get filters from session
     *
     * @param  string $name
     * @return array
     */
    protected function getFilter($name)
    {
        return $this->getRequest()->getSession()->get('filter.' . $name);
    }

    /**
     * Deletes a Group entity.
     *
     */
    public function deleteAction(Group $group, Request $request)
    {
        $form = $this->createDeleteForm($group->getId(), 'admin_groups_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $users = $group->getUsers();
            foreach ($users as $user)
                $user->getGroups()->removeElement($group);
            $em->remove($group);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_groups'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
