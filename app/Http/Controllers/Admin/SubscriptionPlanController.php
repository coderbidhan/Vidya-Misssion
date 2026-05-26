<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubscriptionPlanRequest;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        return $this->successResponse(Subscription::orderBy('price')->get());
    }

    public function store(SubscriptionPlanRequest $request): JsonResponse
    {
        $plan = Subscription::create($request->validated());

        return $this->successResponse($plan, 'Subscription plan created.', 201);
    }

    public function update(SubscriptionPlanRequest $request, int $id): JsonResponse
    {
        $plan = Subscription::findOrFail($id);
        $plan->update($request->validated());

        return $this->successResponse($plan->fresh(), 'Plan updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        Subscription::findOrFail($id)->delete();

        return $this->successResponse(null, 'Plan deleted.');
    }

    /**
     * List all user subscription purchases (filterable by payment_status).
     *
     * GET /api/admin/user-subscriptions
     */
    public function userSubscriptions(Request $request): JsonResponse
    {
        $query = UserSubscription::with(['user:id,name,email', 'subscription:id,name,price'])
            ->when($request->status, fn ($q) => $q->where('payment_status', $request->status))
            ->when($request->search, fn ($q) => $q->whereHas('user', fn ($uq) => $uq->where('email', 'like', '%' . $request->search . '%')))
            ->orderByDesc('created_at');

        return $this->paginatedResponse($query->paginate(20));
    }
}
