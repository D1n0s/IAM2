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
 }else if($option == 4)
 {
     ///
     $filteredItems = [];
     foreach ($d->items as $element) {
             $colorValue = hexdec($element->photo->dominant_color); // Konwersja wartości szesnastkowej na dziesiętną
             if (5611520 < $colorValue && $colorValue < 16744181) {
                 $filteredItems[] = $element;
             }
     }

     $d->items = $filteredItems;
     ///
     //red
//     if(5611520<$hexColor && $hexColor<16744181)
//     {
//     }elseif (78208<$hexColor && $hexColor<16741939)
//     {
//         echo 'green';
//     }elseif (9117184<$hexColor && $hexColor<16775917)
//     {
//         echo  'yellow';
//     }elseif (139<$hexColor && $hexColor<11393254)
//     {
//         echo 'blue';
//     }

 }else if($option == 5)
 {
     ///
     $filteredItems = [];
     foreach ($d->items as $element) {
         $colorValue = hexdec($element->photo->dominant_color); // Konwersja wartości szesnastkowej na dziesiętną
         if (78208<$colorValue && $colorValue<16741939) {
             $filteredItems[] = $element;
         }
     }

     $d->items = $filteredItems;

 }else if($option == 6)
 {
     ///
     $filteredItems = [];
     foreach ($d->items as $element) {
         $colorValue = hexdec($element->photo->dominant_color); // Konwersja wartości szesnastkowej na dziesiętną
         if (9117184<$colorValue && $colorValue<16775917) {
             $filteredItems[] = $element;
         }
     }

     $d->items = $filteredItems;


 }else if($option == 7)
 {
     ///
     $filteredItems = [];
     foreach ($d->items as $element) {
         $colorValue = hexdec($element->photo->dominant_color); // Konwersja wartości szesnastkowej na dziesiętną
         if (139<$colorValue && $colorValue<11393254) {
             $filteredItems[] = $element;
         }
     }

     $d->items = $filteredItems;


 }



    return view('welcome',compact(['d','option']));
})->name('sort');



Route::post('/search', function (Request $request) {
    $contents = \File::get('siema.json');
    $d = json_decode($contents);


    $find = $request->search;
    $d->items = array_filter($d->items, function($element) use ($find) {
        return
            strpos(strtolower($element->title), strtolower($find)) !== false
        || strpos(strtolower($element->user->login), strtolower($find)) !== false
        || strpos(strtolower($element->brand_title), strtolower($find)) !== false
            || strpos(strtolower($element->price), strtolower($find)) !== false;
    });



    return view('welcome', compact('d'));
})->name('search');

Route::post('/searchsize', function (Request $request) {
    $contents = \File::get('siema.json');
    $d = json_decode($contents);

    $find = $request->size;
    if($find == "*"){
        return view('welcome', compact('d'));
    }else{
        $d->items = array_filter($d->items, function($element) use ($find) {
            return strtolower($element->size_title) === strtolower($find);
        });
    }




    return view('welcome', compact('d')); })->name('searchsize');

