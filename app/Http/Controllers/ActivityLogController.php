<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role()->first();

        $userIds = ! $role ? User::where('company_id', compid())->pluck('id')->toArray() : [auth()->id()];

        $activity = Activity::whereIn('causer_id', $userIds)->with('causer');

        if (request()->input('user_id')) {
            $activity->where('causer_id', request()->input('user_id'));
        }
        if (request()->input('subject_type')) {
            $activity->where('subject_type', 'like', '%'.request()->input('subject_type').'%');
        }

        if (! empty(request()->query('sortBy')) && ! empty(request()->query('sortDesc'))) {
            $activity->orderBy(request()->query('sortBy')[0], request()->query('sortDesc')[0] === 'true' ? 'desc' : 'asc');
        }

        if (request()->get('created_at')) {
            if (count(request()->input('created_at')) === 1) {
                $activity->whereDate('created_at', request()->input('created_at')[0]);
            }
            if (count(request()->input('created_at')) == 2) {

                if (Carbon::parse(request()->input('created_at')[0]) < Carbon::parse(request()->input('created_at')[1])) {
                    $activity->whereBetween('created_at', request()->get('created_at'));
                } else {
                    $activity->whereBetween('created_at', array_reverse(request()->get('created_at')));
                }

            }
        }

        return response()->json($activity->latest()->paginate(itemPerPage()))
            ->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
