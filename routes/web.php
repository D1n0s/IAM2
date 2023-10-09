<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

    $contents = \File::get('siema.json');
    $d = json_decode($contents);


    $compareByFavoriteCount = function ($a, $b) {
        if ($a->favourite_count == $b->favourite_count) {
            return 0;
        }
        return -1*($a->favourite_count < $b->favourite_count ? -1 : 1);
    };

    usort($d->items, $compareByFavoriteCount);

//    foreach ($d->items as $item) {
//        echo $item->id . " " . $item->title . " " . $item->favourite_count . "<br>";
//    }
//    dd($d);
    return view('welcome',compact(['d']));
});
Route::post('/sort', function (Request $request) {
    $compareByFavoriteCount = null;
    $contents = \File::get('siema.json');
    $d = json_decode($contents);
    $option = $request->input('option');

 if($option == 1 ){
     $compareByFavoriteCount = function ($a, $b) {
         if ($a->favourite_count == $b->favourite_count) {
             return 0;
         }
         return -1*($a->favourite_count < $b->favourite_count ? -1 : 1);
     };
     usort($d->items, $compareByFavoriteCount);
 }else if($option == 2 ){
     $compareByFavoriteCount = function ($a, $b) {
         if ($a->price == $b->price) {
             return 0;
         }
         return ($a->price < $b->price ) ? -1 : 1;
     };
     usort($d->items, $compareByFavoriteCount);
 }else if($option == 3 ){
     $compareByFavoriteCount = function ($a, $b) {
         if ($a->price == $b->price) {
             return 0;
         }
         return -1*($a->price < $b->price ? -1 : 1);
     };
     usort($d->items, $compareByFavoriteCount);
 }



    return view('welcome',compact(['d','option']));
})->name('sort');
