<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction()
    {
        
    }

    public function mongoInsertAction()
    {
        $mongoString = "mongodb://localhost";
        $m = new Mongo($mongoString);
        $db = $m->testdb;

        $collection = $db->people;


        $mydoc = array();
        $mydoc["name"] = 'Josh';
        $mydoc["tags"] = array("awesome", "super", "great-guy");
        $mydoc["email"] = "josh@joshbutts.com";

        $collection->insert($mydoc);
        
    }

    public function mongoQueryAction()
    {
        $mongoString = "mongodb://localhost";
        $m = new Mongo($mongoString);
        $db = $m->testdb;
        $collection = $db->people;
        $josh = $collection->find(array('tags' => 'great-guy'));
        Zend_Debug::dump($josh->getNext());
        Zend_Debug::dump($josh->count());
    }

    public function mongoDeleteAction()
    {
        $mongoString = "mongodb://localhost";
        $m = new Mongo($mongoString);
        $db = $m->testdb;
        $collection = $db->people;
        $josh = $collection->findOne(array('name' => 'Josh'));
        $collection->remove(array("_id" => $josh["_id"]));
    }


}

