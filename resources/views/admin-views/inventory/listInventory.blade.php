@extends('layouts.admin.app')

@section('title','Update Branch')

@push('css_or_js')

@endpush

@section('content')
<a href="{{route('admin.branch.inventory_form',['branchId'=>$branchId])}}">Add a Product to Inventory</a>
<ul>
        @forelse($inventory as $item)
            <li> <strong>{{$item->product['name']}}</strong> quantity: {{$item['count']}}
                <form action="{{$item->id}}" method="post">
                    @csrf
                    <label for="">Quantity to reduct</label> 
                    <input type="number" name="count" id="" min="1" placeholder="example: 1">
                    <button> -</button>
                </form>
                <form action="{{route('admin.branch.add-to-inventory',['branchId'=>$branchId])}}" method="post">
                    @csrf
                    <input type="hidden" name="product" value="{{$item['product']['id']}}">
                    <label for="">Quantity to add</label> 
                    <input type="number" name="count" id="" min="1" placeholder="example: 1">
                    <button>+</button>
                </form>
            </li>
        @empty
        @endforelse
    </ul>
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
@endpush
