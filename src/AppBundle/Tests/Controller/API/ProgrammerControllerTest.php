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
        $this->assertStringEndsWith('/api/programmers/ObjectOrienter',$response->getHeader('Location'));
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


    public function testPUTProgrammer()
    {
        $this->createProgrammer([
            'nickName' => 'CowboyCoder',
            'avatarNumber' => 6,
            'tagLine' => 'foo'
        ]);

        $data = array(
            'nickname' => 'CowgirlCoder',
            'avatarNumber' => 2,
            'tagLine' => 'foo'
        );

        $response = $this->client->put('api/programmers/CowboyCoder',array(
            'body' => json_encode($data)
        ));
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response,'avatarNumber','2');
        $this->asserter()->assertResponsePropertyEquals($response,'nickname','CowboyCoder');
    }

    public function testDELETEProgrammer()
    {
        $this->createProgrammer([
            'nickName' => 'CowboyCoder',
            'avatarNumber' => 3,
            'tagLine' => 'foo'
        ]);

        $response = $this->client->delete('api/programmers/CowboyCoder');
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testPATCHProgrammer()
    {
        $this->createProgrammer([
            'nickName' => 'CowboyCoder',
            'avatarNumber' => 3,
            'tagLine' => 'bar'
        ]);

        $data = array(
            'tagLine' => 'foo'
        );

        $response = $this->client->patch('api/programmers/CowboyCoder',array(
            'body' => json_encode($data)
        ));
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response,'tagLine','foo');
        $this->asserter()->assertResponsePropertyEquals($response,'avatarNumber',3);
    }
}
