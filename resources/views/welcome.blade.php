<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!-- Styles -->
        <style>
            body{
                margin: 0px;
                background-image: linear-gradient(to right, rgba(51, 50, 77, 0.9), rgba(28, 43, 105, 0.64));
                background-repeat: no-repeat;
                background-attachment: fixed;
            }
    .content{
        width:80%;
        margin-left: auto; margin-right: auto;
    }
    .box{
        padding: 20px;
        float:left;
        background-color: red;
        border-radius: 10px;
        border: 3px solid grey;
        background-color: #ffffff;
        margin-bottom: 3vh;

        min-height: 660px;
    }
    select{
        border:none;
        padding: 5px;
    }


        </style>
    </head>
    <body>

<div style="width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    padding:50px;

    ">
        <form method="post" action="{{route('sort')}}">
            @csrf
            <select name="option"  class="form-select form-select-lg">
                <option value="1">Serduszka</option>
                <option value="2">Cena od najniższej</option>
                <option value="3">Cena od najwyższej</option>
                <option value="4">Czerwony</option>
                <option value="5">Zielony</option>
                <option value="6">Żółty</option>
                <option value="7">Niebieski</option>

            </select>
            <button type="submit">SORTUJ</button>
        </form>
</div>

<div style="width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    padding:50px;

    ">
    <form method="post" action="{{route('search')}}">
        @csrf
        <input type="text" id="search" name="search" autofocus>
        <button type="submit">Wyszukaj</button>
    </form></div>

    <div class="content">
        @foreach($d->items as $item)
        <div class="box">
                <div style="width: 100%;margin:5px;height: 50px;">
                @if(isset($item->user->photo->url))
                <img src="{{$item->user->photo->url}}" width="48" height="48" style="border-radius: 50%;margin:2px;float: left;">
                        <p style="font-size: 20px;">{{$item->user->login}}</p>
                @endif

                    @if(!isset($item->user->photo->url))
                            <p style="text-align: left;font-size: 20px;">{{$item->user->login}}</p>
                    @endif
                </div>

            <div class="photo">
                <a href="{{$item->url}}">
                <img src="{{$item->photo->url}}" alt="" width="290" height="435">
                </a>
            </div>
            <div class="desc" style="width:70%;float:left;">
                <p style="margin-bottom:0px;font-size: 20px;">{{ number_format($item->price,2) }} zł</p>
                <p style="color:blue;margin-top:0px;font-size: 14px;">{{ number_format($item->total_item_price,2) }} zł, w tym <svg fill="blue" width="12" height="12" viewBox="0 0 16 16"><path d="m7.34 8.3 3.56-3.58 1.07 1-4.62 4.67-2.89-2.82 1.06-1Zm7.33-5.9V8c0 5.6-6.67 8-6.67 8s-6.67-2.4-6.67-8V2.4L8 0Zm-1.5 1.05L8 1.59 2.83 3.45V8a6.31 6.31 0 0 0 2.76 4.92 13.09 13.09 0 0 0 2 1.28l.37.17.37-.17a13.09 13.09 0 0 0 2-1.28A6.31 6.31 0 0 0 13.17 8Z"></path></svg></p>
                <p>{{$item->size_title}}</p>
                <p>{{$item->brand_title}}</p>
            </div>
            <div class="desc" style="width:30%;float:left;">
                    <p style="font-size: 30px;text-align: right;"><span class="glyphicon glyphicon-heart-empty"></span>{{$item->favourite_count}}</p>
            </div>
        </div>

        @endforeach
    </div>






    </body>
</html>
