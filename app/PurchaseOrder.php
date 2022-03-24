<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 7/13/17
 * Time: 16:31
 */

namespace App;


use App\Scopes\PurchaseOrderScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PurchaseOrder extends Order
{

    protected $table = 'orders';
    protected $payment_type = 'paid';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new PurchaseOrderScope);
    }

    /**
     * @return boolean
     */
    public function getEditableAttribute()
    {
        $this->load('transactions');
        return ($this->transactions->isEmpty() ? true : false );
    }

    /**
     * @return boolean
     */
    public function getNotEditableAttribute()
    {
        return (! $this->editable);
    }

    public function getCanBeDeletedAttribute()
    {
        foreach($this->batches as $batch) {
            if(bccomp($batch->units_purchased, $batch->inventory, 4) !== 0) return false;
        }
        return true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function cultivator()
    {
        return $this->belongsTo(User::class, 'cultivator_id');
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function originating_entity()
    {
        return $this->belongsTo(User::class, 'bill_to_id');
    }

    public function getOriginatingEntityModelAttribute()
    {
        if(empty($this->originating_entity)) {
            return $this->vendor;
        }
        return $this->originating_entity;
    }

    public function addBatch($batch)
    {
//        if( ! $batch['unit_cost']) {
//            throw new \Exception('Batch requires unit_cost');
////            $batch['unit_cost'] = $batch['total_cost'] / $batch['quantity'];
//        }

        $batchObj = new Batch([
            'purchase_order_id' => $this->id,
            'category_id' => $batch['category_id'],
            'fund_id' => $this->fund_id,
            'license_id' => $this->destination_license_id,
            'tax_rate_id' => $batch['tax_rate_id'],
            'status' => 'Inventory',
            'name' => $batch['name'],
            'type' => !empty($batch['type']) ? $batch['type'] : null,
            'batch_number' => ( ! empty($batch['batch_number']) ? $batch['batch_number'] : null ),
            'ref_number' => ( ! empty($batch['ref_number']) ? $batch['ref_number'] : implode("-", ['BT'.Carbon::now()->format('ny'), mt_rand(10000,99999)]) ),
            'units_purchased' => $batch['quantity'],
            'unit_price' => $batch['unit_cost'],
            'inventory' => $batch['quantity'],
            'uom' => $batch['uom'],
            'is_medical' => ( ! empty($batch['is_medical']) ? ($batch['is_medical']) : 0 ),
            'in_metrc' => 1,
            'cultivator_id' => (stristr($this->customer_type,'cultivator')?$this->vendor_id:null),
            'cultivation_date' => ( ! empty($batch['cultivation_date']) ? Carbon::parse($batch['cultivation_date']) : null ),
            'cost_includes_cult_tax' => ( !empty($batch['tax_rate_id']) ? 1 : 0 ),
            'rnd_link' => ( ! empty($batch['rnd_link']) ? $batch['rnd_link'] : null),
            'thc_rnd' => ( ! empty($batch['thc_rnd']) ? $batch['thc_rnd'] : null),
            'cbn_rnd' => ( ! empty($batch['cbn_rnd']) ? $batch['cbn_rnd'] : null),
        ]);

        $batchObj->calculateCultTax();

        $batchObj->unit_price += $batchObj->unit_tax_amount;
        $batchObj->subtotal_price = $batchObj->inventory * $batchObj->unit_price;
        $batchObj->save();

//        'unit_tax_amount' => $unit_tax_amount,
//        'subtotal_price' => $batch['quantity'] * ($batch['unit_cost'] + $unit_tax_amount),
//        'tax'=>$batch['tax_amount'],

//        dump($tax_rate_quantity);
//        dump($batch['tax_rate']->amount);
//        dump($unit_tax_amount);
//        dump($batch['unit_cost'] + $unit_tax_amount);
//dd('d');


//dd($batch_data);


//        $batchObj = Batch::create($batch_data);
//        $batchObj->cultivation_tax();

        return $batchObj;
    }

    public function updateTotals()
    {
        $this->load('transactions');

        $this->tax = $this->batches->sum('tax');
        $this->subtotal = $this->batches->sum('subtotal_price');

        $this->discount = $this->tax;
        $this->discount_description = ($this->discount>0)?'Cultivation Tax Collected':'';

        $this->total = ($this->subtotal - $this->tax);
        $this->balance = $this->total - $this->transactions->sum('amount');

        if( $this->balance == 0 ) $this->status = 'closed';
        else $this->status = 'open';

        $this->save();
        return;
    }

    public function set_order_id()
    {
        $this->ref_number = $this->new_ref_number('PO');
        $this->save();
        return $this;
    }

    static public function search($q)
    {
        parent::$search_table = 'vendor';
        return parent::search($q);
    }

}