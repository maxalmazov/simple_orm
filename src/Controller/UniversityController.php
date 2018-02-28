<?php

namespace App\Controller;

use App\Request\Request;

class UniversityController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $em = $this->getEntityManager();
        $university = $this->mapper->findAll();

        include __DIR__ . '/../view/main.php';
    }

    public function addAction(Request $request)
    {
        $em = $this->getEntityManager();
        if ($request->isPost()) { //TODO: isFormSubmit() && isValid()
            $university = $this->mapper->doCreateObject($request->getPostVar());
            $em->persist($university);
            $em->flush();
            $request->redirectToRoute('home');
        }

        include __DIR__ . '/../view/university_add.php';
    }

    public function editAction(Request $request)
    {
        $em = $this->getEntityManager();
        $university = $this->mapper->find($request->getUriParams()[2]);
        if ($request->isPost()) { //TODO: isFormSubmit() && isValid()
            $university->setName($request->getPostVar()['name']); //TODO тут иначе сделать
            $university->setCity($request->getPostVar()['city']); //TODO тут иначе сделать
            $university->setFaculties($request->getPostVar()['faculties']); //TODO тут иначе сделать
            $em->persist($university);
            $em->flush();
    
            $request->redirectToRoute('home');
        }

        include __DIR__ . '/../view/university_edit.php';
    }
}
