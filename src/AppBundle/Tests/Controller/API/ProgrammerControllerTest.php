<?php
namespace AppBundle\Tests\Controller\API;

use AppBundle\Test\ApiTestCase;


class ProgrammerControllerTest extends ApiTestCase
{
    public function testPOST()
    {
        $nickName = 'ObjectOrienter' ;
        $data = array(
            'nickName' => $nickName,
            'avatarNumber' => 6,
            'tagLine' => 'a test dev'
        );

         // 1) POST to create the programmer
        $response = $this->client->post('/api/programmers',array(
            'body' => json_encode($data)
        ));

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('/api/programmers/ObjectOrienter',$response->getHeader('Location'));
        $finishedData = json_decode($response->getBody(),true);
        $this->assertArrayHasKey('nickname', $finishedData);
        $this->assertEquals('ObjectOrienter', $finishedData['nickname']);

    }
}
