<?php

namespace App\Http\Services;

use App\Models\RedirectLogModel;
use App\Models\RedirectModel;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class RedirectService
{
    public $id;
    public $request;
    public $redirectLogModel;

    public function __construct(RedirectLogModel $redirectLogModel)
    {
        $this->redirectLogModel = $redirectLogModel;
    }

    public function index() : Collection
    {
        //Retorna todos os registros de redirect
        return RedirectModel::all();
    }

    public function store(array $request) : void
    {
        //Cria o redirect com o request fornecido
        RedirectModel::create($request);
    }

    public function update(array $request, RedirectModel $redirect) : void
    {
        //Atualiza o redirect com o request fornecido
        $redirect->updateOrFail($request);
    }

    public function destroy(RedirectModel $redirect) : void
    {
        //Desativa e executa o soft delete no redirect
        $redirect->update(['active' => 0]);
        $redirect->delete();
    }

    public function redirect(string $ip, ?string $referer, array $query, string $userAgent, RedirectModel $redirect)
    {
        //Verifica se o redirect está desativado, retorna erro se estiver
        if(!$redirect->active){
            throw new Exception('A rota solicitada está desativada.');
        }

        //Monta o retorno com os dados recebidos
        $data = [
            'redirect_id' => $redirect->getRawOriginal('id'),
            'ip' => $ip,
            'referer' => $referer,
            'query_params' => json_encode($query),
            'user_agent' => $userAgent
        ];

        //Salva os logs do redirect
        $this->redirectLogModel->create($data);

        //Utiliza o redirect e o request para montar a url de redirecionamento
        $route = $this->makeUrl($redirect->url_to, $query);

        //Retorna a url de destino
        return $route;
    }

    public function stats(RedirectModel $redirect) : array
    {
        //Instancia logs para o redirect fornecido
        $redirectLog = $this->redirectLogModel->where('redirect_id', $redirect->getRawOriginal('id'))->firstOrFail();

        //Consulta o referer para retornar o mais frequente
        $referer = $redirectLog->select('referer')->whereNotNull('referer')->groupBy('referer')->orderByRaw('COUNT(*) DESC')->first();

        //Retorna os dados de acesso do redirect
        return [
            'access_count' => $redirectLog->count(),
            'unique_access' => $redirectLog->distinct()->count('ip'),
            'top_referer' => !empty($referer)? $referer['referer'] : null,
            'access_total' => $redirectLog->whereDate('created_at', '>=', now()->subDays(10)->toDateString())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, COUNT(DISTINCT ip) as unique_access')
            ->groupBy('date')->orderBy('date')->get(),
        ];
    }

    public function logs(RedirectModel $redirect) : Collection
    {
        //Retorna todos os logs do redirect fornecido
        return $this->redirectLogModel->where('redirect_id', $redirect->getRawOriginal('id'))->get();
    }

    private function makeUrl(string $redirectUrl, array $requestParams) : string
    {
        //Separa as seções da url vinda do redirect
        $redirectUrl = parse_url($redirectUrl);

        //Define o path, se houver
        $path = isset($redirectUrl['path'])? $redirectUrl['path'] : '';

        //Monta a url sem queries no modelo https://host.com/path?
        $url = $redirectUrl['scheme'] . '://' . $redirectUrl['host'] . $path . '?';

        //Monta a string de queries do request
        foreach($requestParams as $key=>$param)
        {
            if(!empty($param))
            {
                $url .= $key . '=' . $param . '&';
            }
        }

        //Verifica se há queries vindas do redirect
        if(isset($redirectUrl['query']))
        {
            //Define as queries do redirect em array
            $redirectParams = explode('&', $redirectUrl['query']);
    
            //Monta a string de queries do redirect
            foreach($redirectParams as $key=>$param)
            {
                //Captura o nome da chave e valor do parâmetro
                $paramName = explode('=', $param)[0];
                $paramValue = explode('=', $param)[1];

                //Verifica se o request enviado contem uma chave válida (não vazia) igual à chave atual
                if(!array_key_exists($paramName, $requestParams) || empty($requestParams[$paramName]))
                {
                    //Se não houver, adiciona o parâmetro à url
                    $url .= $paramName . '=' . $paramValue . '&';
                }
            }
        }
        
        //Remove o caractere final da url
        $url = rtrim($url, '&');

        //Retorna a url criada
        return $url;
    }
}
