<?php
namespace Front\Controller;

use Front\Model\Job;
use Front\Model\Category;
use Front\Model\JobTable;
use Front\Model\CategoryTable;
use Zend\Db\ResultSet\ResultSet;
use Front\Controller\IndexController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;

class IndexControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;
    
    protected function setUp()
    {
        $bootstrap = \Zend\Mvc\Application::init(include 'config/application.config.php');
        
        $categoryData = array('id_category' => 1, 'name' => 'Project Manager');
        $category     = new Category();
        $category->exchangeArray($categoryData);
        $resultSetCategory = new ResultSet();
        $resultSetCategory->setArrayObjectPrototype(new Category());
        $resultSetCategory->initialize(array($category));
        $mockCategoryTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('select'), array(), '', false);
        $mockCategoryTableGateway->expects($this->any())
                         ->method('select')
                         ->with()
                         ->will($this->returnValue($resultSetCategory));
        $categoryTable = new CategoryTable($mockCategoryTableGateway);

        $jobData = array(
            'id_job' => 1,
            'id_category' => 1,
            'type' => 'typeTest',
            'company' => 'companyTest',
            'logo' => 'logoTest',
            'url' => 'urlTest',
            'position' => 'positionTest',
            'location' => 'locaitonTest',
            'description' => 'descriptionTest',
            'how_to_play' => 'hotToPlayTest',
            'is_public' => 1,
            'is_activated' => 1,
            'email' => 'emailTest',
            'created_at' => '2012-01-01 00:00:00',
            'updated_at' => '2012-01-01 00:00:00'
        );
        $job = new Job();
        $job->exchangeArray($jobData);
        $resultSetJob = new ResultSet();
        $resultSetJob->setArrayObjectPrototype(new Job());
        $resultSetJob->initialize(array($job));
        $mockJobTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('select'), array(), '', false);
        $mockJobTableGateway->expects($this->any())
                            ->method('select')
                            ->with(array('id_category' => 1))
                            ->will($this->returnValue($resultSetJob));
        $jobTable = new JobTable($mockJobTableGateway);
        
        $this->controller = new IndexController($categoryTable, $jobTable);
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event      = $bootstrap->getMvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setEventManager($bootstrap->getEventManager());
        $this->controller->setServiceLocator($bootstrap->getServiceManager());
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->routeMatch->setParam('action', 'index');
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
    
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
    }

    public function test404WhenActionDoesNotExist()
    {
        $this->routeMatch->setParam('action', 'action-qui-nexiste-pas');
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
}