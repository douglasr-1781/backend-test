<?php

namespace Tests\Unit;

use App\Http\Services\RedirectService;
use App\Models\RedirectLogModel;
use App\Models\RedirectModel;
use Mockery;
use PHPUnit\Framework\TestCase;

class RedirectTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_redirect_success()
    {        
        $redirect = (new RedirectModel)->setRawAttributes(['id' => 1, 'url_to' => 'https://google.com/search', 'active' => 1]);

        $redirectLog = Mockery::mock(RedirectLogModel::class);
        $redirectLog->shouldReceive('create')
            ->once()
            ->andReturn();

        $response = (new RedirectService($redirectLog))->redirect('192.168.0.1', 'teste', ['query'=>'true'], 'PostmanRuntime/7.36.3', $redirect);

        $this->assertEquals($response, 'https://google.com/search?query=true');
    }

    public function test_redirect_param_merge_success()
    {        
        $redirect = (new RedirectModel)->setRawAttributes(['id' => 1, 'url_to' => 'https://google.com?utm_campaign=ads', 'active' => 1]);

        $redirectLog = Mockery::mock(RedirectLogModel::class);
        $redirectLog->shouldReceive('create')
            ->once()
            ->andReturn();

        $response = (new RedirectService($redirectLog))->redirect('192.168.0.1', 'teste', ['utm_source'=>'facebook'], 'PostmanRuntime/7.36.3', $redirect);

        $this->assertEquals($response, 'https://google.com?utm_source=facebook&utm_campaign=ads');
    }

    public function test_redirect_param_merge_priority()
    {        
        $redirect = (new RedirectModel)->setRawAttributes(['id' => 1, 'url_to' => 'https://google.com?utm_source=facebook&utm_campaign=ads', 'active' => 1]);

        $redirectLog = Mockery::mock(RedirectLogModel::class);
        $redirectLog->shouldReceive('create')
            ->once()
            ->andReturn();

        $response = (new RedirectService($redirectLog))->redirect('192.168.0.1', 'teste', ['utm_source'=>'instagram'], 'PostmanRuntime/7.36.3', $redirect);

        $this->assertEquals($response, 'https://google.com?utm_source=instagram&utm_campaign=ads');
    }

    public function test_redirect_param_merge_ignore_empty_params()
    {        
        $redirect = (new RedirectModel)->setRawAttributes(['id' => 1, 'url_to' => 'https://google.com?utm_source=facebook&utm_campaign=ads', 'active' => 1]);

        $redirectLog = Mockery::mock(RedirectLogModel::class);
        $redirectLog->shouldReceive('create')
            ->once()
            ->andReturn();

        $response = (new RedirectService($redirectLog))->redirect('192.168.0.1', 'teste', ['utm_source'=>null], 'PostmanRuntime/7.36.3', $redirect);

        $this->assertEquals($response, 'https://google.com?utm_source=facebook&utm_campaign=ads');
    }
}
