<?php

namespace Tests\Feature;

use App\Models\RedirectLogModel;
use App\Models\RedirectModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RedirectLogTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
    }

    public function test_stats_unique_access()
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
                $json->where('unique_access', 1)
                    ->etc()
            );

        $response->assertStatus(200);
    }
    
    public function test_stats_referer_count()
    {
        RedirectModel::factory()->create(['id' => 1]);
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
    
    public function test_stats_ten_day_count_return_has_data()
    {
        RedirectModel::factory()->create(['id' => 1]);

        RedirectLogModel::factory()->create(['redirect_id' => 1, 'ip' => '192.0.0.1','created_at' => Carbon::now()->format('Y-m-d'),]);

        $response = $this->get('/api/redirects/jR/stats');

        $response
            ->assertJsonStructure([
                'access_count',
                'unique_access',
                'top_referer',
                'access_total',
            ])->assertJson(fn (AssertableJson $json) =>
                $json->where('access_total', [
                    ['date' => Carbon::now()->format('Y-m-d'),
                    'total' => 1,
                    'unique_access' => 1]
                ])
                    ->etc()
            );

        $response->assertStatus(200);
    }
    
    public function test_stats_ten_day_count_empty_return_data()
    {
        RedirectModel::factory()->create(['id' => 1]);

        RedirectLogModel::factory()->count(3)->create(['redirect_id' => 1, 'created_at' => Carbon::now()->subDays(30)->format('Y-m-d'),]);

        $response = $this->get('/api/redirects/jR/stats');

        $response
            ->assertJsonStructure([
                'access_count',
                'unique_access',
                'top_referer',
                'access_total',
            ])->assertJson(fn (AssertableJson $json) =>
                $json->where('access_total', [])
                    ->etc()
            );

        $response->assertStatus(200);
    }
    
    public function test_stats_ten_day_count_return_data()
    {
        RedirectModel::factory()->create(['id' => 1]);

        RedirectLogModel::factory()->create(['redirect_id' => 1, 'ip' => '192.0.0.1','created_at' => Carbon::now()->format('Y-m-d'),]);
        RedirectLogModel::factory()->create(['redirect_id' => 1, 'ip' => '192.0.0.2','created_at' => Carbon::now()->format('Y-m-d')]);
        RedirectLogModel::factory()->count(2)->create(['redirect_id' => 1, 'ip' => '192.0.0.1', 'created_at' => Carbon::now()->subDays(1)->format('Y-m-d')]);
        RedirectLogModel::factory()->create(['redirect_id' => 1, 'created_at' => Carbon::now()->subDays(2)->format('Y-m-d')]);
        RedirectLogModel::factory()->create(['redirect_id' => 1, 'created_at' => Carbon::now()->subDays(30)->format('Y-m-d')]);

        $response = $this->get('/api/redirects/jR/stats');

        $response
            ->assertJsonStructure([
                'access_count',
                'unique_access',
                'top_referer',
                'access_total',
            ])->assertJson(fn (AssertableJson $json) =>
                $json->where('access_total', [
                    ['date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                    'total' => 1,
                    'unique_access' => 1],
                    ['date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                    'total' => 2,
                    'unique_access' => 1],
                    ['date' => Carbon::now()->format('Y-m-d'),
                    'total' => 2,
                    'unique_access' => 2],
                ])
                    ->etc()
            );

        $response->assertStatus(200);
    }
    
    public function test_stats_ten_day_count_return_data_period()
    {
        RedirectModel::factory()->create(['id' => 1]);

        RedirectLogModel::factory()->create(['redirect_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d'),]);
        RedirectLogModel::factory()->create(['redirect_id' => 1, 'created_at' => Carbon::now()->subDays(30)->format('Y-m-d')]);

        $response = $this->get('/api/redirects/jR/stats');

        $response
            ->assertJsonStructure([
                'access_count',
                'unique_access',
                'top_referer',
                'access_total',
            ])->assertJson(fn (AssertableJson $json) =>
                $json->where('access_total', [
                    ['date' => Carbon::now()->format('Y-m-d'),
                    'total' => 1,
                    'unique_access' => 1],
                ])
                    ->etc()
            );

        $response->assertStatus(200);
    }
}
