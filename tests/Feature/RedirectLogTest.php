<?php

namespace Tests\Feature;

use App\Models\RedirectLogModel;
use App\Models\RedirectModel;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RedirectLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_unique_access()
    {
        RedirectModel::factory()->create();
        RedirectLogModel::factory()->count(13)->create(['redirect_id' => 1, 'ip' => '192.0.0.1']);
        var_dump(RedirectModel::first());die;
        $response = $this->get('/api/redirects/jR/stats');
        print_r($response->dd());die;
        $response
            ->assertJsonStructure([
                'access_count',
                'unique_access',
                'top_referer',
                'access_total',
            ])->assertJson(fn (AssertableJson $json) =>
                $json->where('unique_access', 1)
                    ->etc()
            );

        $response->assertStatus(200);
    }
    
    public function test_stats_referer_count()
    {
        RedirectModel::factory()->create();
        RedirectLogModel::factory()->count(13)->create(['redirect_id' => 1, 'ip' => '192.0.0.1']);

        $response = $this->get('/api/redirects/jR/stats');

        $response
            ->assertJsonStructure([
                'access_count',
                'unique_access',
                'top_referer',
                'access_total',
            ])->assertJson(fn (AssertableJson $json) =>
                $json->where('top_referer', 'a')
                    ->etc()
            );

        $response->assertStatus(200);
    }
}
