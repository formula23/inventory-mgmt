<?php

namespace App\Http\Controllers;

use App\Batch;
use App\OrderDetail;
use App\SaleOrder;
use App\TransferLog;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->all();

        try {
            DB::beginTransaction();

//dd($data);
            $sale_order = SaleOrder::find($data['_sale_order_id']);

            $batch = Batch::find($data['batch_id']);
//            if(!$batch) {
//                throw new \Exception('No Batch found. Try again.');
//            }

            if(!is_null($data['_unit_markup']) && $batch) {
                $data['unit_sale_price'] = ($batch->unit_price + $data['_unit_markup']);
            } elseif ($data['_unit_sale_price'] != 0) {
                $data['unit_sale_price'] = $data['_unit_sale_price'];
            }
//dd($data);
            if(empty($data['unit_sale_price'])) {
                throw new \Exception("Please enter a sales price.");
            }

            if (!empty($data['_sold_as_name_input'])) {
                $data['sold_as_name'] = $data['_sold_as_name_input'];
            }

//            dd($data);

            $sale_order->addUpdateItem($batch, $data['_sold_as_name_input'], $data['units'], $data['unit_sale_price']);

            $sale_order->calculateTotals();

            flash()->success($data['_sold_as_name_input'].' added to order.');

            DB::commit();

        } catch (QueryException $e) {
            DB::rollBack();
            flash()->error('Unable to save item. '. $e->getMessage());
        } catch(\Exception $e) {
            DB::rollBack();
            flash()->error($e->getMessage());
        }

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderDetail $orderDetail)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            if(Arr::has($data, 'cog') && $data['cog']==1) {

                if (isset($data['units'])) {

                    if ($data['units'] == 0) {
                        throw new \Exception('To delete an item, use the delete button.');
                    }

                    $data['units_accepted'] = null;

                    $unit_change = (float)bcsub($orderDetail->units, $data['units'], 4);

                    $orderDetail->batch->inventory = bcadd($orderDetail->batch->inventory, $unit_change, 4);

                    if ($orderDetail->batch->inventory < 0) {
                        throw new \Exception('Updated quantity exceeds available.');
                    }
                    $orderDetail->batch->save();
                }

                if (Arr::has($data, '_markup')) {
                    $data['unit_sale_price'] = bcadd($orderDetail->unit_cost, $data['_markup'], 2);
                }
            }
            else
            {
                $data['units_accepted'] = $data['units'];
            }

//        $data['subtotal_sale_price'] = $data['units'] * $orderDetail['unit_sale_price'];

            $orderDetail->update($data);

            $orderDetail->sale_order->calculateTotals();

            DB::commit();
            flash()->success('Item updated');

        } catch (QueryException $e) {
            DB::rollBack();
            flash()->error('Unable to update item. '. $e->getMessage());
        } catch(\Exception $e) {
            DB::rollBack();
            flash()->error($e->getMessage());
        }

        return back();
    }

    public function retag(Request $request, OrderDetail $orderDetail)
    {

        /// add units back to original batch
//        $orderDetail->batch->inventory = bcadd($orderDetail->batch->inventory, $orderDetail->units, 4);
        $original_batch = $orderDetail->batch;

        /// retag original batch
        $uid = config('highline.metrc_tag')[$original_batch->license_id].str_pad( (int)$request->get('tag_id'), 9, 0, STR_PAD_LEFT);

        $qty_to_xfer = $orderDetail->units;
        $used_weight = ($original_batch->uom == 'g') ? $qty_to_xfer : $qty_to_xfer * config('highline.uom.lb');

        //amount
        $amount = $orderDetail->units;
        $uom = $original_batch->uom;

        $packages_created = [
            [
                "ref_number"=>$uid,
                "category_id" => $original_batch->category_id,
                "brand_id" => null,
                "amount" => $amount,
                "uom" => $uom,
                "packed_date" => Carbon::today(),
                "fund_id" => $original_batch->fund_id,
            ]
        ];

//            dump($packages_created);

        try {

            $new_batch = $original_batch->transfer(
                $used_weight,
                $qty_to_xfer,
                $packages_created
            );

            if($new_batch instanceof Batch)
            {
                $new_batch->inventory = 0;
                $new_batch->save();

                $orderDetail->batch_id = $new_batch->id;
                $orderDetail->save();
            }

        } catch(\Exception $e) {
            DB::rollBack();
            flash()->error($e->getMessage());

            return redirect()->back();
        }

        flash()->success('Batch '.$original_batch->ref_number.' retagged to: '.$new_batch->ref_number);
        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
