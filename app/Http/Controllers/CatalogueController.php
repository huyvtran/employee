<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CatalogueRequest;
use App\Http\Resources\CatalogueResource;
use App\Models\Catalogue;
use DateTime;
use Illuminate\Support\Str;
class CatalogueController extends Controller
{
    //GET CATALOGUE
	public function getCatalogue($id) {
		$catalogues = Store::findorFail($id)->catalogues()->orderByPriority('desc')->orderByName('asc')->get();
		$res        = [
			'type'    => 'success',
			'message' => 'Get catalogue successfully',
			'data'    => CatalogueResource::collection($catalogues)
		];

		return $this->respondSuccess('Get catalogue', $catalogues, 200, 'many');
	}

	protected function respondSuccess($message, $data, $status = 200, $type)
    {
        $res = [
            'type'    => 'success',
            'message' => $message. ' successfully.',
        ];

        switch ($type) {
            case 'one':
            $res['catalogue']  = new CatalogueResource($data);
            break;

            case 'many':
            $res['catalogues'] = CatalogueResource::collection($data);
            break;
        }

        return response($res, $status);
    }
}
