<tr>
    <td data-toggle="collapse" data-target="#group-{{ $category['category_id'] }}-{{ clean_string_strict($batches->first()->uom) }}-{{ clean_string_strict($batches->first()->name) }}-{{ $batches->first()->brand_id }}-{{ $loop->iteration }}"><a href="javascript:void(null);"><i class=" mdi mdi-library-plus"></i></a></td>
    <td></td>
    <td>{{ $batches->first()->present()->branded_name }}</td>
    <td><!-- coa --></td>
    {{--<td><!-- coa --></td>--}}
    {{--<td>{!! display_potency_results($batches) !!}<!-- thc --></td>--}}
    <td>{!! display_inventory($batches) !!}</td>
    <td colspan="9"><!-- package date --></td>
</tr>