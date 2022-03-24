<div class="row">

        <div class="col-4">

            <div class=" form-group">

                <input class="form-control" type="text" name="_batches[ref_number][]" value="" placeholder="METRC / Unique ID">
            </div>

            <div class=" form-group">
                {{ Form::select('_batches[category_id][]', $categories->pluck('name','id')->toArray(), null, ['class'=>'form-control']) }}
            </div>

            <div class="row form-group">
                <div class="col-lg-6">
                    <input type="text" class="form-control" name="_batches[name][]" value="" placeholder="Strain Name" required="required">
                </div>
                <div class="col-lg-6">
                    <input type="text" class="form-control" name="_batches[description][]" value="" placeholder="Short Name">
                </div>

            </div>

        </div>

        <div class="col-4">

            <div class="row form-group">
                <div class="col-lg-6">
                    <input type="text" class="form-control quantity" name="_batches[quantity][]" value="" placeholder="Qty" required="required">
                </div>
                <div class="col-lg-6">
                    {{ Form::select("_batches[uom][]", array_combine(array_keys(config('highline.uom')), array_keys(config('highline.uom'))), null, ['class'=>'form-control']) }}
                </div>
            </div>

            <div class="row form-group">

                <div class="input-group bootstrap-touchspin col-lg-6">
                    <span class="input-group-addon bootstrap-touchspin-prefix">$</span>
                    <input type="text" value="" name="_batches[unit_cost][]" class="form-control unit_cost" style="display: block;" placeholder="Pre-Tax Unit Cost" required="required">
                </div>

                <div class="input-group bootstrap-touchspin col-lg-6">
                    <span class="input-group-addon bootstrap-touchspin-prefix">$</span>
                    <input type="text" value="" name="_batches[total_cost][]" class="form-control total_cost" style="display: block;" placeholder="Total Cost" required="required">
                </div>
            </div>

            <div class="form-group cult-tax-row">
                {{ Form::select("_batches[tax_rate_id][]", $tax_rates->prepend('- Cultivation Tax Rate -',''), null, ['class'=>'form-control tax_rate_id']) }}
                <small>Tax rate will be added to the pre-tax unit cost</small>
            </div>

        </div>

        <div class="col-4">

            <div class=" form-group">
                <input class="form-control" type="text" name="_batches[batch_number][]" value="" placeholder="Internal Batch / Lot #">

            </div>

            <div class="row form-group">
                <label class="col-lg-3 col-form-label text-right">Cultivation Date:</label>
                <div class="col-lg-9">
                    <input class="form-control" type="date" name="_batches[cultivation_date][]" value="">
                </div>
            </div>
        </div>

        {{--<hr>--}}

        {{--<div style="display: none">--}}
            {{--<h5 class="header-title">R&D Results</h5>--}}

            {{--<div class="row form-group">--}}

                {{--<div class="col-6">--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="rnd_link">Link</label>--}}
                        {{--{{ Form::input('text', 'batches[rnd_link][]', null, ['class'=>'form-control', 'placeholder'=>'Link']) }}--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="row form-group">--}}

                {{--<div class="col-2">--}}
                    {{--<label for="thc_rnd">THC</label>--}}
                    {{--<div class="input-group bootstrap-touchspin">--}}
                        {{--<input id="thc_rnd" type="number" value="" name="_batches[thc_rnd][]" min="0" max="100.00" step="0.01" class="form-control col-lg-10" style="display: block;" placeholder="0.00">--}}
                        {{--<span class="input-group-addon bootstrap-touchspin-postfix">%</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-2">--}}
                    {{--<label for="cbd_rnd">CBD</label>--}}
                    {{--<div class="input-group bootstrap-touchspin">--}}
                        {{--<input id="cbd_rnd" type="number" value="" name="_batches[cbd_rnd][]" step="0.01" min="0" max="100" class="form-control col-lg-10" style="display: block;" placeholder="0.00">--}}
                        {{--<span class="input-group-addon bootstrap-touchspin-postfix">%</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-2">--}}
                    {{--<label for="cbn_rnd">CBN</label>--}}
                    {{--<div class="input-group bootstrap-touchspin">--}}
                        {{--<input id="cbn_rnd" type="number" value="" name="_batches[cbn_rnd][]" step="0.01" min="0" max="100" class="form-control col-lg-10" style="display: block;" placeholder="0.00">--}}
                        {{--<span class="input-group-addon bootstrap-touchspin-postfix">%</span>--}}
                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}

        {{--</div>--}}



</div>