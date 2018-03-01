<?php

namespace App\Controller;

use App\Application;
use App\Entity\University;
use App\Request\Request;
use App\ORM\Manager\EntityManager;

class UniversityController extends AbstractController
{
    public function indexAction(Request $request)
    {

        $mapper = Application::instance()->getMapper(University::class);
        $university = $mapper->findAll();

        include __DIR__ . '/../view/main.php';
    }

    public function addAction(Request $request)
    {
        $em = EntityManager::instance();
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
        $em = EntityManager::instance();
        $mapper = $em->getMapper(University::class);
        $university = $mapper->find($request->getUriParams()[2]);
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
