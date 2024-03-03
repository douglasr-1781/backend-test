<?php

namespace App\Http\Services;

use App\Models\RedirectLogModel;
use App\Models\RedirectModel;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Vinkla\Hashids\Facades\Hashids;

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
}
