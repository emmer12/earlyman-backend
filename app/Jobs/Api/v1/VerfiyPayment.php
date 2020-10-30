<?php

namespace App\Jobs\Api\v1;

use Yabacon\Paystack;
use App\Models\Payment; 
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VerfiyPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $key;

    protected $payment_ref;

    protected $user;

    protected $amount;

    protected $duration;

    protected $plan;

    protected $property_id;
    
    public function __construct($payment_ref, $amount, $duration, $plan, $user, $property_id)
    {
        $this->key = 'sk_test_eb548593e745f932fb25b04c83ee77e5c9f9eb4c';
        $this->payment_ref = $payment_ref;
        $this->user = $user;
        $this->property_id = $property_id;
        $this->amount = $amount;
        $this->duration = $duration;
        $this->plan = $plan;
    }

    public function handle()
    {
         $paystack = new Paystack($this->key);

         $trx = $paystack->transaction->verify(['reference' => $this->payment_ref]);

        if($trx->data->status == 'success') {
            if (count(Payment::where('payment_ref', $this->payment_ref)->get()) == 1){
              return true;
            } else {
                $payment =  Payment::create([
                    'payment_ref' => $this->payment_ref,
                    'user_id' => $this->user->id,
                    'property_id' => $this->property_id,
                    'amount' => $this->amount,
                    'payment_status' => $trx->data->status,
                    'comment' => 'Approved',
                    'transaction_number' => $trx->data->transaction_date,
                    'isCurrent' => true,
                    'duration' => $this->duration,
                    'plan' => $this->plan
                ]);
                if ($payment) {
                    return true;
                }else {
                    return false;
                }
            }
        }
    }
}
