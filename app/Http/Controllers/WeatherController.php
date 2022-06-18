<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{

    function fetch() {
        $result = array();

        $latitude = -1.2832533;
        $longitude = 36.8172449;
        $weather = $this->weather($latitude, $longitude);
        if (is_array($weather) && sizeof($weather) > 0) {
            if (strlen(trim($weather['weather'])) > 0) {
                $result['condition'] = trim($weather['weather']);
            }
            if (strlen(trim($weather['description'])) > 0) {
                $result['description'] = trim($weather['description']);
            }
            if (strlen(trim($weather['temp'])) > 0) {
                $result['temp'] = trim($weather['temp']);
            }
            if (strlen(trim($weather['temp_high'])) > 0) {
                $result['temp_high'] = trim($weather['temp_high']);
            }
            if (strlen(trim($weather['temp_low'])) > 0) {
                $result['temp_low'] = trim($weather['temp_low']);
            }
        }
        else {

        }
        $result['city'] = 'Nairobi';
        $result['country_code'] = 'KE';
        return view('index')->with('data', $result);
    }

    public function get(Request $request) {
        $result = array();
        if (strlen(trim($request->input('city'))) > 0 && strlen(trim($request->input('country'))) > 0) {
            $city = $request->input('city');
            $country = $request->input('country');
        }
        else {
            $location = $this->client_country();
            $city = $location['city'];
            $country = $location['country'];
        }

        $geo = $this->geo($city, $country);
        if (is_array($geo) && sizeof($geo) > 0){
            $latitude = $geo['latitude'];
            $longitude = $geo['longitude'];
            if (strlen(trim($geo['latitude'])) > 0 && strlen(trim($geo['longitude'])) > 0) {
                $weather = $this->weather($latitude, $longitude);
                if (is_array($weather) && sizeof($weather) > 0) {
                    if (strlen(trim($weather['weather'])) > 0) {
                        $condition = trim($weather['weather']);
                    }
                    if (strlen(trim($weather['description'])) > 0) {
                        $description = trim($weather['description']);
                    }
                    if (strlen(trim($weather['temp'])) > 0) {
                        $temp = trim($weather['temp']);
                    }
                    if (strlen(trim($weather['temp_high'])) > 0) {
                        $temp_high = trim($weather['temp_high']);
                    }
                    if (strlen(trim($weather['temp_low'])) > 0) {
                        $temp_low = trim($weather['temp_low']);
                    }
                    $city = ucwords($city);
                    $country_code = strtoupper($country);

                    //if (strlen(trim($weather['weather'])) > 0) {
                    //    $result['condition'] = trim($weather['weather']);
                    //}
                    //if (strlen(trim($weather['description'])) > 0) {
                    //    $result['description'] = trim($weather['description']);
                    //}
                    //if (strlen(trim($weather['temp'])) > 0) {
                    //    $result['temp'] = trim($weather['temp']);
                    //}
                    //if (strlen(trim($weather['temp_high'])) > 0) {
                    //    $result['temp_high'] = trim($weather['temp_high']);
                    //}
                    //if (strlen(trim($weather['temp_low'])) > 0) {
                    //    $result['temp_low'] = trim($weather['temp_low']);
                    //}
                    //$result['city'] = ucwords($city);
                    //$result['country_code'] = strtoupper($country);
                }
                return redirect('/')->with(['weather' => $condition, 'description' => $description, 'temp' => $temp, 'temp_high' => $temp_high, 'temp_low' => $temp_low, 'city' => $city, 'country_code' => $country_code]);
                //return redirect('/')->with($result);
                //print_r($result);
            }

        }
        else {
            return redirect('/')->with('input-error', 'Invalid city and/or country');
        }
    }

    public function client_address() {
        $ip = null;

        if (array_key_exists('X-Forwarded-For', $_SERVER) && filter_var($_SERVER['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = $_SERVER['X-Forwarded-For'];
        }
        else {
            if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else {
                $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            }
        }
        return($ip);
    }


    public function client_country() {
        $result = array();

        $ip = $this->client_address();
        $response = file_get_contents('http://ipinfo.io/'.$ip.'/geo');
        $response = json_decode($response, true);

        if (isset($response['country'])) {
            $result['country']  = $response['country'];
        }
        else {
            $result['country'] = 'Kenya';
        }

        if (isset($response['city'])) {
            $result['city']  = $response['city'];
        }
        else {
            $result['city'] = 'Nairobi';
        }
        return($result);
    }

    public function geo($city = null, $country = null) {
        $result = array();

        if (strlen(trim($city)) > 0 && strlen(trim($country)) > 0) {
            $limit = 1;
            $url = 'http://api.openweathermap.org/geo/1.0/direct?q='.strtolower(trim($city)).','.strtolower(trim($country)).'&limit='.$limit.'&appid='.env('WEATHER_API_KEY').'';
            $response = Http::get($url)->json();

            if (is_array($response) && sizeof($response) > 0) {
                foreach($response as $i => $j) {
                    if (strlen(trim($j['lat'])) > 0 && strlen(trim($j['lon'])) > 0 ) {
                        $result['latitude'] = trim($j['lat']);
                        $result['longitude'] = trim($j['lon']);
                    }
                }
            }
        }
        return ($result);
    }


    public function weather($latitude = null, $longitude = null) {
        $result = array();

        if (strlen(trim($latitude)) > 0 && strlen(trim($longitude)) > 0) {
            $url = 'https://api.openweathermap.org/data/2.5/weather?lat='.trim($latitude).'&lon='.trim($longitude).'&appid='.env('WEATHER_API_KEY').'&units=metric';
            $response = Http::get($url)->json();

            if (strlen(trim($response['name'])) > 0) {
                $result['city'] = trim($response['name']);
            }

            if (is_array($response['sys']) && sizeof($response['sys']) > 0) {
                if (strlen(trim($response['sys']['country'])) > 0) {
                    $result['country_code'] = trim($response['sys']['country']);
                }
            }

            if (is_array($response['weather']) && sizeof($response['weather']) > 0) {
                foreach($response['weather'] as $i => $j) {
                    if (isset($j['main']) && strlen(trim($j['main'])) > 0 ) {
                        $result['weather'] = trim($j['main']);
                    }
                    if (isset($j['description']) && strlen(trim($j['description'])) > 0 ) {
                        $result['description'] = trim($j['description']);
                    }
                }
            }

            if (is_array($response['main']) && sizeof($response['main']) > 0) {
                if (isset($response['main']['temp']) && strlen(trim($response['main']['temp'])) > 0 ) {
                    $result['temp'] = trim($response['main']['temp']);
                }
                if (isset($response['main']['temp_max']) && strlen(trim($response['main']['temp_max'])) > 0 ) {
                    $result['temp_high'] = trim($response['main']['temp_max']);
                }
                if (isset($response['main']['temp_min']) && strlen(trim($response['main']['temp_min'])) > 0 ) {
                    $result['temp_low'] = trim($response['main']['temp_min']);
                }
            }

        }
        return ($result);
    }
}
