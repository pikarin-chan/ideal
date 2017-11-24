<?php

namespace imc\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/users/');
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr'));
        $crawler = $client->click($crawler->filter('.new_entry a')->link());
        $form = $crawler->filter('form button[type="submit"]')->form(array(
            'user[username]' => 'Lorem ipsum dolor sit amet',
            'user[usernameCanonical]' => 'Lorem ipsum dolor sit amet',
            'user[email]' => 'Lorem ipsum dolor sit amet',
            'user[emailCanonical]' => 'Lorem ipsum dolor sit amet',
            'user[enabled]' => true,
            'user[salt]' => 'Lorem ipsum dolor sit amet',
            'user[password]' => 'Lorem ipsum dolor sit amet',
            'user[lastLogin]' => new \DateTime(),
            'user[locked]' => true,
            'user[expired]' => true,
            'user[expiresAt]' => new \DateTime(),
            'user[confirmationToken]' => 'Lorem ipsum dolor sit amet',
            'user[passwordRequestedAt]' => new \DateTime(),
            'user[roles]' => 'Lorem ipsum dolor sit amet',
            'user[credentialsExpired]' => true,
            'user[credentialsExpireAt]' => new \DateTime(),
            'user[loginCount]' => 42,
            'user[firstLogin]' => new \DateTime(),
                    ));
        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->click($crawler->filter('.record_actions a')->link());
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr'));
    }

    public function testCreateError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/users/new');
        $form = $crawler->filter('form button[type="submit"]')->form();
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('form div.has-error')->count());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @depends testCreate
     */
    public function testEdit()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/users/');
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr:contains("First value")'));
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr:contains("Changed")'));
        $crawler = $client->click($crawler->filter('table.records_list tbody tr td .btn-group a')->eq(1)->link());
        $form = $crawler->filter('form button[type="submit"]')->form(array(
            'user[username]' => 'Changed',
            'user[usernameCanonical]' => 'Changed',
            'user[email]' => 'Changed',
            'user[emailCanonical]' => 'Changed',
            'user[enabled]' => true,
            'user[salt]' => 'Changed',
            'user[password]' => 'Changed',
            'user[lastLogin]' => new \DateTime(),
            'user[locked]' => true,
            'user[expired]' => true,
            'user[expiresAt]' => new \DateTime(),
            'user[confirmationToken]' => 'Changed',
            'user[passwordRequestedAt]' => new \DateTime(),
            'user[roles]' => 'Changed',
            'user[credentialsExpired]' => true,
            'user[credentialsExpireAt]' => new \DateTime(),
            'user[loginCount]' => 42,
            'user[firstLogin]' => new \DateTime(),
            // ... adapt fields value here ...
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->click($crawler->filter('.record_actions a')->link());
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr:contains("First value")'));
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr:contains("Changed")'));
    }

    /**
     * @depends testCreate
     */
    public function testEditError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/users/');
        $crawler = $client->click($crawler->filter('table.records_list tbody tr td .btn-group a')->eq(1)->link());
        $form = $crawler->filter('form button[type="submit"]')->form(array(
            'user[field_name]' => '',
            // ... use a required field here ...
        ));
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('form div.has-error')->count());
    }

    /**
     * @depends testCreate
     */
    public function testDelete()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/users/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr'));
        $crawler = $client->click($crawler->filter('table.records_list tbody tr td .btn-group a')->eq(0)->link());
        $client->submit($crawler->filter('form#delete button[type="submit"]')->form());
        $crawler = $client->followRedirect();
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr'));
    }

    /**
     * @depends testCreate
     */
    public function testFilter()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/users/');
        $form = $crawler->filter('div#filter form button[type="submit"]')->form(array(
            'user_filter[username]' => 'First%',
            'user_filter[usernameCanonical]' => 'First%',
            'user_filter[email]' => 'First%',
            'user_filter[emailCanonical]' => 'First%',
            'user_filter[enabled]' => true,
            'user_filter[salt]' => 'First%',
            'user_filter[password]' => 'First%',
            'user_filter[lastLogin]' => new \DateTime(),
            'user_filter[locked]' => true,
            'user_filter[expired]' => true,
            'user_filter[expiresAt]' => new \DateTime(),
            'user_filter[confirmationToken]' => 'First%',
            'user_filter[passwordRequestedAt]' => new \DateTime(),
            'user_filter[roles]' => 'First%',
            'user_filter[credentialsExpired]' => true,
            'user_filter[credentialsExpireAt]' => new \DateTime(),
            'user_filter[loginCount]' => 42,
            'user_filter[firstLogin]' => new \DateTime(),
            // ... maybe use just one field here ...
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $crawler = $client->click($crawler->filter('div#filter a')->link());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
    }    /**
     * @depends testCreate
     */
    public function testSort()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/users/');
        $this->assertCount(1, $crawler->filter('table.records_list th')->eq(0)->filter('a i.fa-sort'));
        $crawler = $client->click($crawler->filter('table.records_list th a')->link());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('table.records_list th a i.fa-sort-up'));
    }
}
