<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\FacadesHttp;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class VintedController extends Controller
{

    public function index()
    {

        $d = $this->pagination($this->fetchData());

        //

        return view('welcome', compact('d'));
    }


    private $URL = "https://www.vinted.pl/";

    public function fetchData()
    {
        $cookie = $this->getCookie();

        $query = request()->query('query');
        $page = request()->query('page');

        if ($cookie) {
            $dd = $this->getData(
                $cookie,
                $query,
                $page
            );

            if ($dd) {

                //działa
                //dd($d['items']);
                return $dd['items'];
                //return view('welcome', compact('d'));
            } else {
                return response()->json(['error' => 'Błąd przy pobieraniu danych.'], 500);
            }
        } else {
            return response()->json(['error' => 'Błąd przy pobieraniu ciasteczka.'], 500);
        }
    }

    private function getData($cookie, $query, $page)
    {
        try {
            $link = "";
            if (!empty($query) && !empty($page)) {
                $link = "https://www.vinted.pl/api/v2/catalog/items?search_text=" . $query . "&page=" . $page;
            } else {
                $link = "https://www.vinted.pl/api/v2/catalog/items?search_text=adidas";
            }

            $response = Http::withHeaders([
                'Cookie' => '_vinted_fr_session=' . $cookie,
            ])->get($link);

            $response2 = Http::withHeaders([
                'Cookie' => '_vinted_fr_session=' . $cookie,
            ])->get("https://www.vinted.pl/api/v2/catalog/items?search_text=" . $query . "&page=1");

            $response3 = Http::withHeaders([
                'Cookie' => '_vinted_fr_session=' . $cookie,
            ])->get("https://www.vinted.pl/api/v2/catalog/items?search_text=" . $query . "&page=2");

            $response4 = Http::withHeaders([
                'Cookie' => '_vinted_fr_session=' . $cookie,
            ])->get("https://www.vinted.pl/api/v2/catalog/items?search_text=" . $query . "&page=" . $page + 3);

            $response5 = Http::withHeaders([
                'Cookie' => '_vinted_fr_session=' . $cookie,
            ])->get("https://www.vinted.pl/api/v2/catalog/items?search_text=" . $query . "&page=" . $page + 4);

            $items = array_merge($response->json()['items'], $response2->json()['items'], $response3->json()['items'], $response4->json()['items'], $response5->json()['items']);

            $dataArr = array_merge($response->json(), $response2->json(), $response3->json(), $response4->json(), $response5->json());
            $dataArr['items'] = $items;

            return $dataArr;
        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }
    }

    private function pagination($dane)
    {

        $naStrone = 10;

        $strona = request()->get('page', 1);

        $indeksPoczatkowy = ($strona - 1) * $naStrone;

        $daneStrony = array_slice($dane, $indeksPoczatkowy, $naStrone);

        $kolekcjaDanych = new Collection($daneStrony);

        $d = new LengthAwarePaginator(
            $kolekcjaDanych,
            count($dane),
            $naStrone,
            $strona,
            ['path' => request()->url()]
        );

        return $d;
    }

    private function getCookie()
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER => true,     //return headers in addition to content
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING => "",       // handle all encodings
            CURLOPT_AUTOREFERER => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT => 120,      // timeout on response
            CURLOPT_MAXREDIRS => 30,       // stop after 10 redirects
            CURLINFO_HEADER_OUT => true,
            CURLOPT_SSL_VERIFYPEER => true,     // Validate SSL Certificates
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        );

        $ch = curl_init($this->URL);
        curl_setopt_array($ch, $options);
        $rough_content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header_content = substr($rough_content, 0, $header['header_size']);
        $body_content = trim(str_replace($header_content, '', $rough_content));
        $pattern = "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m";
        preg_match_all($pattern, $header_content, $matches);
        $cookiesOut = implode("; ", $matches['cookie']);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['headers'] = $header_content;
        $header['content'] = $body_content;
        $header['cookies'] = $cookiesOut;

        $link = $header['cookies'];
        $pos_start = strpos($link, '_vinted_fr_session');
        $sub_string = substr($link, $pos_start);
        $pos_end = strpos($sub_string, ';');
        $sub_string = substr($sub_string, 19, $pos_end - 19);
        return $sub_string;
    }

    public function sort(Request $request)
    {
        $compareByFavoriteCount = null;


        $d = $this->fetchData();
        $option = $request->input('option');

        if ($option == 1) {
            $compareByFavoriteCount = function ($a, $b) {
                if ($a['favourite_count'] == $b['favourite_count']) {
                    return 0;
                }
                return -1 * ($a['favourite_count'] < $b['favourite_count'] ? -1 : 1);
            };
            usort($d, $compareByFavoriteCount);
        } else if ($option == 2) {
            $compareByFavoriteCount = function ($a, $b) {
                if ($a['price'] == $b['price']) {
                    return 0;
                }
                return ($a['price'] < $b['price']) ? -1 : 1;
            };
            usort($d, $compareByFavoriteCount);
        } else if ($option == 3) {
            $compareByFavoriteCount = function ($a, $b) {
                if ($a['price'] == $b['price']) {
                    return 0;
                }
                return -1 * ($a['price'] < $b['price'] ? -1 : 1);
            };
            usort($d, $compareByFavoriteCount);
        } else if ($option == 4) {
            ///
            $filteredItems = [];
            foreach ($d as $element) {
                $colorValue = hexdec($element['photo']['dominant_color']); // Konwersja wartości szesnastkowej na dziesiętną
                if (5611520 < $colorValue && $colorValue < 16744181) {
                    $filteredItems[] = $element;
                }
            }

            $d = $filteredItems;

        } else if ($option == 5) {
            ///
            $filteredItems = [];
            foreach ($d as $element) {
                $colorValue = hexdec($element['photo']['dominant_color']); // Konwersja wartości szesnastkowej na dziesiętną
                if (78208 < $colorValue && $colorValue < 16741939) {
                    $filteredItems[] = $element;
                }
            }

            $d = $filteredItems;
        } else if ($option == 6) {
            ///
            $filteredItems = [];
            foreach ($d as $element) {
                $colorValue = hexdec($element['photo']['dominant_color']); // Konwersja wartości szesnastkowej na dziesiętną
                if (9117184 < $colorValue && $colorValue < 16775917) {
                    $filteredItems[] = $element;
                }
            }

            $d = $filteredItems;

        } else if ($option == 7) {
            ///
            $filteredItems = [];
            foreach ($d as $element) {
                $colorValue = hexdec($element['photo']['dominant_color']); // Konwersja wartości szesnastkowej na dziesiętną
                if (139 < $colorValue && $colorValue < 11393254) {
                    $filteredItems[] = $element;
                }
            }

            $d = $filteredItems;

        }

        $d = $this->pagination($d);
        return view('welcome', compact(['d', 'option']));

    }

    public function search(Request $request)
    {


        $d = $this->fetchData();
        $find = $request->search;
        $d = array_filter($d, function ($element) use ($find) {
            return
                strpos(strtolower($element['title']), strtolower($find)) !== false
                || strpos(strtolower($element['user']['login']), strtolower($find)) !== false
                || strpos(strtolower($element['brand_title']), strtolower($find)) !== false
                || strpos(strtolower($element['price']), strtolower($find)) !== false;
        });

        $d = $this->pagination($d);
        return view('welcome', compact('d'));
    }


    public function searchsize(Request $request)
    {


        $d = $this->fetchData();


        $find = $request->size;
        if ($find == "*") {
            return view('welcome', compact('d'));
        } else {
            $d = array_filter($d, function ($element) use ($find) {
                return strtolower($element['size_title']) === strtolower($find);
            });
        }
        $d = $this->pagination($d);

        return view('welcome', compact('d'));

    }
}
