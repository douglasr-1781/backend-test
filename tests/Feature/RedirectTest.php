<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_create_redirect_success(): void
    {
        $newRedirect = [
            'url_to' => 'https://google.com'
        ];

        $response = $this->post('/api/redirects', $newRedirect);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('mensagem', 'Redirect criado com sucesso.')
                ->etc()
            );

        $response->assertStatus(200);
    }
    
    public function test_create_redirect_invalid_url_error(): void
    {
        $newRedirect = [
            'url_to' => 'url invalida'
        ];

        $response = $this->post('/api/redirects', $newRedirect);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('mensagem', 'Url inválida.')
                ->etc()
            );

        $response->assertStatus(400);
    }
    
    public function test_create_redirect_internal_url_error(): void
    {
        $newRedirect = [
            'url_to' => 'https://localhost:8000'
        ];

        $response = $this->post('/api/redirects', $newRedirect);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('mensagem', 'A url enviada deve ser externa à aplicação.')
                ->etc()
            );

        $response->assertStatus(400);
    }
    
    public function test_create_redirect_non_https_url_error(): void
    {
        $newRedirect = [
            'url_to' => 'http://google.com'
        ];

        $response = $this->post('/api/redirects', $newRedirect);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('mensagem', 'A url deve utilizar o protocolo https.')
                ->etc()
            );

        $response->assertStatus(400);
    }
    
    public function test_create_redirect_url_status_error(): void
    {
        $newRedirect = [
            'url_to' => 'https://x.com'
        ];

        $response = $this->post('/api/redirects', $newRedirect);

        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('mensagem', 'Url não retornou status 200.')
                ->etc()
            );

        $response->assertStatus(400);
    }
}
