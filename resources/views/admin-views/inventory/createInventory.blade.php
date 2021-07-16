@extends('layouts.admin.app')

@section('title','Update Branch')

@push('css_or_js')

@endpush

@section('content')
<form method="post">
        @csrf
        <div>
            <label for="product">Select A Product</label>
            <select name="product" id="product" required>
                <option selected disabled >--SELECT--</option>
                @forelse($products as $product)
                    <option value="{{$product['id']}}">{{$product['name']}}</option>
                @empty
                    <option disabled>Nothing to show</option>
                @endforelse
            </select>
        </div>
        <div class="">
            <label for="count">Quantity</label>
            <input type="number" name="count" id="count" value="1">

        </div>
        <button>Add to Inventory</button>
    </form>
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
