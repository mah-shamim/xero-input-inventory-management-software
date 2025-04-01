<?php

namespace App\Http\Controllers;

use App\Traits\AverageCalculation;
use App\Traits\GlobalQuery;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, GlobalQuery, AverageCalculation;

    /**
     * Check app.php db_beginsTransaction value,
     * if false then it suppose to execute without
     * db transaction for development environment to see
     * error detail on db transaction example mass Assignment.
     * @param $service
     * @param $request
     * @param $params
     * @return string
     * @throws \Exception
     */
    public function renderJsonOutput($service, $request, $params)
    {
        try {
            DB::beginTransaction();
            $result = $service->executePreCondition($request, $params);
            if ($result['type'] == "error") {
                $result = $service->buildFailure($result);
                return json_encode($result);
            }
            $result = $service->execute($result, $request);
            if ($result['type'] == "error") {
                $result = $service->buildFailure($result);
                return json_encode($result);
            }
            $result = $service->executePostCondition($result, $request);
            if ($result['type'] == "error") {
                $result = $service->buildFailure($result);
                return json_encode($result);
            }
            $result = $service->buildSuccess($result);
            DB::commit();
            return json_encode($result, JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = ['type' => 'error', 'message' => $e->getMessage()];
            throw $e;
//            return json_encode($result);
        }
    }

    /**
     * @throws \Exception
     */
    public function renderArrayOutput($service, $request, $params)
    {
        try {
            DB::beginTransaction();
            $result = $service->executePreCondition($request, $params);
            if ($result['type'] == "error") {
                $result = $service->buildFailure($result);
                return $result;
            }
            $result = $service->execute($result, $request);
            if ($result['type'] == "error") {
                $result = $service->buildFailure($result);
                return $result;
            }
            $result = $service->executePostCondition($result, $request);
            if ($result['type'] == "error") {
                $result = $service->buildFailure($result);
                return $result;
            }
            $result = $service->buildSuccess($result);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            $result = [
                'type' => 'error',
                'message' => $e->getMessage(),
                'status'=>$e->getCode()
            ];
            throw $e;
//            return $result;
        }
    }

    public function mappingData($object, $key, $totalKeyName='total')
    {
        $date = $object->map(function ($item) use ($key) {
            return $item->$key;
        });
        $total = $object->map(function ($item) use ($totalKeyName) {
            return (float) $item->$totalKeyName;
        });
        return array($date, $total);
    }

    public function checkAuthorization($report, $method)
    {
        if(session()->has('permissions')){
            $permissions = session()->get('permissions');
            foreach ($permissions as $permission){
                if($permission['model']===$report && $permission['method']===$method){
                    return true;
                }
            }
            abort(403, 'Unauthorized action.');
        }else{
            return true;
        }
    }

    public function orderByQuery($model): void
    {
        if (!empty(request()->query('sortBy')) && !empty(request()->query('sortDesc'))) {
            $model->orderBy(request()->query('sortBy')[0], request()->query('sortDesc')[0] === 'true' ? 'desc' : 'asc');
        }
    }

}
