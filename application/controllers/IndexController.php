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

    public function couchInsertAction()
    {
        $mydoc = array();
        $mydoc["name"] = 'Josh';
        $mydoc["tags"] = array("awesome", "super", "great-guy");
        $mydoc["email"] = "josh@joshbutts.com";   
        
        $database = 'test';
        $id = md5($mydoc["name"]);
        $mydoc["_id"] = $id;

        $data = json_encode($mydoc);

        $url = 'http://localhost:5984/' . $database . '/' . $id;


        $http = new Zend_Http_Client();
        $http->setUri($url);
        $http->setMethod('PUT');
        $http->setRawData($data);
        $response = $http->request();

        $decoded = $response->getBody();
        $decoded = json_decode($decoded);

        Zend_Debug::dump($decoded);

    }

    public function couchCreateViewAction()
    {
        $view["_id"] = "_design/sample";
        $view["language"] = "javascript";
        $view["views"]["byname"]["map"] = "function(doc) { emit(doc.name); }";

        $http = new Zend_Http_Client();
        $http->setUri('http://localhost:5984/test/'. $view["_id"]);
        $http->setMethod('PUT');
        $http->setRawData(json_encode($view));
        $http->request();

    }

    public function couchQueryAction()
    {
        $uri = 'http://localhost:5984/test/_design/sample/_view/byname';
        $http = new Zend_Http_Client();
        $http->setUri($uri);
        $http->setParameterGet(array('key'=>'"Josh"'));
        $http->setMethod('GET');
        $response = $http->request();
        $decoded = json_decode($response->getBody());
        Zend_Debug::dump($decoded);
    }

    public function couchDeleteAction()
    {
        $name = 'Josh';
        $idToDelete = md5($name);
        Zend_Debug::dump($idToDelete);
        $uri = 'http://localhost:5984/test/' . $idToDelete;
        $http = new Zend_Http_Client();
        $http->setUri($uri);
        $response = $http->setMethod('GET');

        $response = $http->request();
        $decoded = json_decode($response->getBody());
        Zend_Debug::dump($decoded);

        $http->setMethod('DELETE');
        $http->setUri('http://localhost:5984/test/' . $idToDelete . '?rev=' . $decoded->_rev);
        $response = $http->request();
        $decoded = json_decode($response->getBody());
        Zend_Debug::dump($decoded);

    }
}

