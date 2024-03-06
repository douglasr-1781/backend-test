<?php

namespace App\Http\Controllers;

use App\Http\Requests\RedirectCreateRequest;
use App\Http\Requests\RedirectUpdateRequest;
use App\Http\Services\RedirectService;
use App\Models\RedirectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class RedirectController extends Controller
{
    private $redirectService;

    public function __construct(RedirectService $redirectService)
    {
        $this->redirectService = $redirectService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Chama o Service para listar os redirects
        $response = $this->redirectService->index();

        //Retorna a lista dos redirects
        return response($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RedirectCreateRequest $request)
    {
        //Chama o Service para criar o redirect
        $this->redirectService->store($request->validated());

        //Retorna mensagem de sucesso
        return response(['mensagem' => 'Redirect criado com sucesso.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RedirectModel  $redirect
     * @return \Illuminate\Http\Response
     */
    public function show(RedirectModel $redirect)
    {
        //Retorna os dados do redirect fornecido
        return response($redirect, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RedirectModel  $redirect
     * @return \Illuminate\Http\Response
     */
    public function update(RedirectUpdateRequest $request, RedirectModel $redirect)
    {
        //Chama o Service para atualizar o redirect
        $this->redirectService->update($request->validated(), $redirect);

        //Retorna mensagem de sucesso
        return response(['mensagem' => 'Redirect atualizado com sucesso.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RedirectModel  $redirect
     * @return \Illuminate\Http\Response
     */
    public function destroy(RedirectModel $redirect)
    {
        //Chama o Service para excluir o redirect (com soft delete)
        $this->redirectService->destroy($redirect);

        //Retorna mensagem de sucesso
        return response(['mensagem' => 'Redirect excluído com sucesso.'], 200);
    }

    /**
     * Redirects user to specified url.
     *
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    public function redirect(Request $request, RedirectModel $redirect)
    {
        //Define os parâmetros de request para criar o log de acesso
        $ip = $request->ip();
        $referer = $request->header('referer');
        $query = $request->query();
        $userAgent = $request->header('User-Agent');

        //Chama o Service para criar a url de redirecionamento (e salvar o log)
        $route = $this->redirectService->redirect($ip, $referer, $query, $userAgent, $redirect);

        //Retorna o redirect para a rota recebida
        return Redirect::to($route);
    }

    /**
     * Redirects user to specified url.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stats(RedirectModel $redirect)
    {
        //Chama o Service para retornar status do redirect
        $response = $this->redirectService->stats($redirect);
        
        //Retorna os status para o redirect fornecido
        return response($response, 200);
    }

    /**
     * Redirects user to specified url.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logs(RedirectModel $redirect)
    {
        //Chama o Service para listar logs do redirect
        return $this->redirectService->logs($redirect);
    }
}
