<?php

namespace App\Jobs\Api\v1;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\PromotedPost;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteOutdatedPromotedPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\User
     */
    protected $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function handle()
    {
        $oldPosts = [];
        $payments = Payment::all();

        foreach ($payments as $payment) {
            $end_date = $payment->created_at->addDays($payment->duration);   
            if (Carbon::now() > $end_date) {
                $payment->isCurrent = false;
                $payment->save();
                array_push($oldPosts, $payment->property_id);
            }
        }

        foreach ($oldPosts as $oldPost) {
            if ($promoted_post) {
                $promoted_post->delete();   
            }
        }

        return true;


        // $payment = Payment::where('user_id', $this->user->id)->latest('created_at')->first();

        // if ($payment) {
        //     $end_date = $payment->created_at->addDays($payment->duration);

        //     if (Carbon::now() > $end_date) {
        //         $payment->isCurrent = false;
        //         $payment->save();

        //         $promoted_posts = PromotedPost::where('user_id', $this->user->id)->whereDate('end_date', '=', $end_date->toDateString())->get();

        //         foreach ($promoted_posts as $promoted_post) {
        //             $promoted_post->delete();
        //         }
        //     }
        // }
        
    }
}
