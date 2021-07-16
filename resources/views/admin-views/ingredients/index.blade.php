@extends('layouts.admin.app')

@section('title','Update Branch')

@push('css_or_js')

@endpush

@section('content')

<a href="{{route('admin.ingredient.list')}}">Check the ingredient list</a>
    <form method="post" action="{{route('admin.ingredient.store')}}" enctype="multipart/form-data">
        @csrf
        <input type="text" name="name" id="" required placeholder="Name of ingredient">
        <input type="number" name="price" required placeholder="Price">
        <input type="number" name="minimum" required placeholder="Minimum">

        <input type="file" name="image">

        <button>Add</button>
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
