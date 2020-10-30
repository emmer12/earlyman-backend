<?php

namespace App\Http\Controllers\Api\v1;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PromotedPost;
use Illuminate\Http\Request;
use App\Jobs\Api\v1\VerfiyPayment;
use App\Http\Controllers\Controller;
use App\Jobs\Api\v1\SubscribeToPromotion;
use App\Jobs\Api\v1\DeleteOutdatedPromotedPost;

class PromotionController extends Controller
{
    public function subscribe(Request $request)
    {
        // userid, duration, plan, transaction number
        $payment_ref = $request->get('payment_ref');
        $amount = $request->get('amount');
        $duration = $request->get('duration');
        $plan = $request->get('plan');
        $property_id = $request->get('property_id');

        // Confirm and save transaction number in payeents table.
        $isVerified = VerfiyPayment::dispatchNow($payment_ref, $amount, $duration, $plan, auth()->user(), $property_id);

        if (!$isVerified) {
            return response()->json([
                'success' => true,
                'code' => 'PAYMENT_VERIFICATION_ERROR',
                'message' => 'An error occurred while verifiying your payment',
                'data' => []
            ], 400);
        }

        // Get all the users post and save them to promoted posts table
        $subscription = SubscribeToPromotion::dispatchNow($plan, $duration, auth()->user(), $property_id);

        return response()->json([
            'success' => true,
            'code' => 'PROMOTION_SUBSCRIPTION_SUCCESSFUL',
            'message' => 'You have successfully subscribed to this promotion plan.',
            'data' => []
        ], 200);
    }

    public function getRandomProperties()
    {
        DeleteOutdatedPromotedPost::dispatchNow();

        $promoted = PromotedPost::with('property')
                                        ->orderByRaw('RAND()')->take(5)->get();

        return response()->json([
            'success' => true,
            'code' => 'PROMOTED_PROPERTIES',
            'message' => 'List of promoted properties',
            'data' => compact('promoted')
        ], 200);
    }

    public function getRandomProperty()
    {
        $promoted = PromotedPost::with('property')
                                        ->orderByRaw('RAND()')->take(1)->get();

        if ($promoted->count() == 0) {
            $promoted = Property::orderByRaw('RAND()')->take(1)->get();
        }

        return response()->json([
            'success' => true,
            'code' => 'PROMOTED_PROPERTY',
            'message' => 'One promoted property post.',
            'data' => compact('promoted')
        ], 200);
    }

    public function updateViewCount(Request $request)
    {
        $user_id = $request->get('user_id');
        $property_id = $request->get('property_id');

        $property = PromotedPost::where('user_id', $user_id)->where('property_id', $property_id)->first();

        $property->views++;

        $property->save();

        return response()->json([
            'success' => true,
            'code' => 'POST_VIEWED',
            'message' => 'Post viewed',
            'data' => [
                'user_id' => $user_id,
                'property_id' => $property_id
            ]
        ], 200);
    }

    public function showMyPromotedProperties()
    {
        $promoted = PromotedPost::with('property')->where('user_id', auth()->user()->id)->get();

        return response()->json([
            'success' => true,
            'code' => 'PROMOTED_PROPERTIES',
            'message' => 'List of promoted properties',
            'data' => compact('promoted')
        ], 200);
    }

    public function showSubscriptionDetails()
    {
        $subscription_details = Payment::latest()->where('user_id', auth()->user()->id)->first();

        return response()->json([
            'success' => true,
            'code' => 'SUBSCRIPTION_DETAILS',
            'message' => 'View logged in user subscription details.',
            'data' => compact('subscription_details')
        ], 200);
    }
}
