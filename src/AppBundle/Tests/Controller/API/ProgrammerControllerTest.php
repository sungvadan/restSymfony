<?php
namespace AppBundle\Tests\Controller\API;



use AppBundle\Test\ApiTestCase;


class ProgrammerControllerTest extends ApiTestCase
{

    protected function setUp(){
        parent::setUp();

        $this->createUser('weaverryan');

    }

    public function testPOST()
    {
        $nickname = 'ObjectOrienter' ;
        $data = array(
            'nickname' => $nickname,
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


    public function testGETProgrammer()
    {
        $this->createProgrammer([
            'nickName' => 'UnitTester',
            'avatarNumber' => 6,
            'tagLine' => 'a test dev'
        ]);

        $response = $this->client->get('api/programmers/UnitTester');
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, ['nickname','avatarNumber','powerLevel','tagLine']);
        $this->asserter()->assertResponsePropertyEquals($response,'nickname','UnitTester');
    }




    public function testGETProgrammersCollection()
    {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ));
        $this->createProgrammer(array(
            'nickname' => 'CowboyCoder',
            'avatarNumber' => 5,
        ));

        $response = $this->client->get('/api/programmers');
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyIsArray($response,'programmers');
        $this->asserter()->assertResponsePropertyCount($response,'programmers',2);
        $this->asserter()->assertResponsePropertyEquals($response,'programmers[1].nickname','CowboyCoder');

    }

}
