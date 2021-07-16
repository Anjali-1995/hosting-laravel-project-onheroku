<!--<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">-->
  <!--  <meta name="viewport" content="width=device-width, initial-scale=1">-->
    <!-- CSRF Token -->
   <!-- <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Razorpay Payment Gateway Integration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>-->
    <!-- Scripts -->
   <!-- <script src="{{ asset('js/app.js') }}" defer></script>-->
    <!-- Fonts -->
  <!--  <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
   
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <main class="py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-3 col-md-offset-6">
                       @if($message = Session::get('error'))
                            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>Error!</strong> {{ $message }}
                            </div>
                        @endif
                        @if($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissible fade {{ Session::has('success') ? 'show' : 'in' }}" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>Success!</strong> {{ $message }}
                            </div>
                        @endif 
                        {!! Session::forget('success') !!}
                             
                        <div class="card card-default">
                            <div class="card-header">
                                Razorpay Payment Gateway Integration
                            </div> 

                            <div class="card-body text-center">
                                <form action="/payment" method="POST" >
                                    @csrf
                                    <script src="https://checkout.razorpay.com/v1/checkout.js"
                                            data-key="{{ env('RAZOR_KEY') }}"
                                            data-amount="$order('order_amount')"
                                            data-buttontext="Pay"
                                            data-name="Atrium"
                                            data-description="razor"
                                            data-image="{{ asset('/image/nice.png') }}"
                                            data-prefill.name="$users('f_name')"
                                            data-prefill.email="$users('email')"
                                            data-theme.color="#9FE2BF">
                                    </script>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>-->
@extends('layouts.blank')

@section('content')
<div id="app">
        <main class="py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-3 col-md-offset-6">
  
                        @if($message = Session::get('error'))
                            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>Error!</strong> {{ $message }}
                            </div>
                        @endif
  
                        @if($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissible fade {{ Session::has('success') ? 'show' : 'in' }}" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>Success!</strong> {{ $message }}
                            </div>
                        @endif
  
                        <div class="card card-default">
                            <div class="card-header">
                                Laravel - Razorpay Payment Gateway Integration
                            </div>
  
                            <div class="card-body text-center">
                               
                               
                                <form action="{{route('payment-razor',['order_id'=>$data['order_id']])}}" method="POST" >
                                    @csrf
                                    <script src="https://checkout.razorpay.com/v1/checkout.js"
                                            data-key="{{ env('RAZORPAY_KEY') }}"
                                            data-amount="{{$data['amount']}}"
                                            data-buttontext="Pay {{$data['amount_to_show']}} {{$data['currency']}}"
                                            data-name="Atrium Food"
                                            data-description="Rozerpay"
                                            data-image="https://atrium.ai/wp-content/uploads/elementor/thumbs/Atrium-logo_h-on59mxwwalflw5ztvi90nmqezphvtc50umwe4vysn2.png"
                                            data-prefill.name="{{$data['user']['f_name']}} {{$data['user']['l_name']}}"
                                            data-prefill.email="{{$data['user']['email']}}"
                                            data-theme.color="#ff7529">
                                    </script>
                                </form>
                            </div>
                        </div>
  
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
