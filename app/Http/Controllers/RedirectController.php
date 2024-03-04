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
        $response = $this->redirectService->index();

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
        $this->redirectService->store($request->validated());

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
        $this->redirectService->update($request->validated(), $redirect);

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
        $this->redirectService->destroy($redirect);

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
        $ip = $request->ip();
        $referer = $request->header('referer');
        $query = $request->query();
        $userAgent = $request->header('User-Agent');

        $route = $this->redirectService->redirect($ip, $referer, $query, $userAgent, $redirect);

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
        $response = $this->redirectService->stats($redirect);
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
        return $this->redirectService->logs($redirect);
    }
}
