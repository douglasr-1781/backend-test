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
        return RedirectModel::all();
    }

    public function store(array $request) : void
    {
        RedirectModel::create($request);
    }

    public function update(array $request, RedirectModel $redirect) : void
    {
        $redirect->updateOrFail($request);
    }

    public function destroy(RedirectModel $redirect) : void
    {
        $redirect->update(['active' => 0]);
        $redirect->delete();
    }

    public function redirect(string $ip, ?string $referer, array $query, string $userAgent, RedirectModel $redirect)
    {
        if(!$redirect->active){
            throw new Exception('A rota solicitada está desativada.');
        }

        $data = [
            'redirect_id' => $redirect->getRawOriginal('id'),
            'ip' => $ip,
            'referer' => $referer,
            'query_params' => json_encode($query),
            'user_agent' => $userAgent
        ];

        $this->redirectLogModel->create($data);

        $route = $this->makeUrl($redirect->url_to, $query);

        return $route;
    }

    public function stats(RedirectModel $redirect) : array
    {
        $redirectLog = $this->redirectLogModel->where('redirect_id', $redirect->getRawOriginal('id'))->firstOrFail();

        $referer = $redirectLog->select('referer')->whereNotNull('referer')->groupBy('referer')->orderByRaw('COUNT(*) DESC')->first();

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
        return $this->redirectLogModel->where('redirect_id', $redirect->getRawOriginal('id'))->get();
    }

    private function makeUrl(string $originalUrl, array $params) : string
    {
        $originalUrl = parse_url($originalUrl);

        $string = $originalUrl['scheme'] . '://' . $originalUrl['host'] . '?';

        foreach($params as $key=>$param)
        {
            if(!empty($param))
            {
                $string .= $key . '=' . $param . '&';
            }
        }

        $string = $string .= $originalUrl['query'];

        return $string;
    }
}
