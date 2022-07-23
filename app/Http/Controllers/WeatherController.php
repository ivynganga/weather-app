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
        $current_weather = $this->current_weather($latitude, $longitude);
        $forecast = $this->forecast_weather($latitude, $longitude);
        if (is_array($current_weather) && sizeof($current_weather) > 0) {
            if (strlen(trim($current_weather['weather'])) > 0) {
                $result['condition'] = trim($current_weather['weather']);
            }
            if (strlen(trim($current_weather['description'])) > 0) {
                $result['description'] = trim($current_weather['description']);
            }
            if (strlen(trim($current_weather['temp'])) > 0) {
                $result['temp'] = trim($current_weather['temp']);
            }
            if (strlen(trim($current_weather['temp_high'])) > 0) {
                $result['temp_high'] = trim($current_weather['temp_high']);
            }
            if (strlen(trim($current_weather['temp_low'])) > 0) {
                $result['temp_low'] = trim($current_weather['temp_low']);
            }
        }

        if (is_array($forecast) && sizeof($forecast) > 0) {
            $count = 0;
            foreach ($forecast as $i => $j) {

                if (strlen(trim($j['date'])) > 0) {
                    $result['forecast'][$count]['date'] = trim($j['date']);
                }
                if (strlen(trim($j['weather'])) > 0) {
                    $result['forecast'][$count]['condition'] = trim($j['weather']);
                }
                if (strlen(trim($j['description'])) > 0) {
                    $result['forecast'][$count]['description'] = trim($j['description']);
                }
                if (strlen(trim($j['temp'])) > 0) {
                    $result['forecast'][$count]['temp'] = trim($j['temp']);
                }
                if (strlen(trim($j['temp_high'])) > 0) {
                    $result['forecast'][$count]['temp_high'] = trim($j['temp_high']);
                }
                if (strlen(trim($j['temp_low'])) > 0) {
                    $result['forecast'][$count]['temp_low'] = trim($j['temp_low']);
                }
                $count++;
            }
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
                $current_weather = $this->current_weather($latitude, $longitude);
                if (is_array($current_weather) && sizeof($current_weather) > 0) {
                    if (strlen(trim($current_weather['weather'])) > 0) {
                        $condition = trim($current_weather['weather']);
                    }
                    if (strlen(trim($current_weather['description'])) > 0) {
                        $description = trim($current_weather['description']);
                    }
                    if (strlen(trim($current_weather['temp'])) > 0) {
                        $temp = trim($current_weather['temp']);
                    }
                    if (strlen(trim($current_weather['temp_high'])) > 0) {
                        $temp_high = trim($current_weather['temp_high']);
                    }
                    if (strlen(trim($current_weather['temp_low'])) > 0) {
                        $temp_low = trim($current_weather['temp_low']);
                    }
                    $city = ucwords($city);
                    $country_code = strtoupper($country);

                }
                return redirect('/')->with(['current_weather' => $condition, 'description' => $description, 'temp' => $temp, 'temp_high' => $temp_high, 'temp_low' => $temp_low, 'city' => $city, 'country_code' => $country_code]);
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

    public function geo($city, $country) {
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


    public function current_weather($latitude = null, $longitude = null) {
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

    function forecast_weather($latitude, $longitude) {
        $result = array();

        if (strlen(trim($latitude)) > 0 && strlen(trim($longitude)) > 0) {
            $url = 'api.openweathermap.org/data/2.5/forecast?lat='.trim($latitude).'&lon='.trim($longitude).'&appid='.env('WEATHER_API_KEY').'&units=metric';
            $response = Http::get($url)->json();
            if ($response['cod'] == 200 && is_array($response['list']) && sizeof($response['list']) > 0) {
                $count = 0;
                foreach ($response['list'] as $i => $j) {
                    $forecast_date = date('Y-m-d H:i', $j['dt']);
                    $noon = explode(' ', $forecast_date);
                    $noon = $noon[1];
                
                    if ($noon == '12:00') {
                        $result[$count]['date'] = $forecast_date;
                        if (is_array($j['weather']) && sizeof($j['weather']) > 0) {
                            foreach($j['weather'] as $k => $l) {
                                if (isset($l['main']) && strlen(trim($l['main'])) > 0 ) {
                                    $result[$count]['weather'] = trim($l['main']);
                                }
                                if (isset($l['description']) && strlen(trim($l['description'])) > 0 ) {
                                    $result[$count]['description'] = trim($l['description']);
                                }
                            }
                        }
            
                        if (is_array($j['main']) && sizeof($j['main']) > 0) {
                            if (isset($j['main']['temp']) && strlen(trim($j['main']['temp'])) > 0 ) {
                                $result[$count]['temp'] = trim($j['main']['temp']);
                            }
                            if (isset($j['main']['temp_max']) && strlen(trim($j['main']['temp_max'])) > 0 ) {
                                $result[$count]['temp_high'] = trim($j['main']['temp_max']);
                            }
                            if (isset($j['main']['temp_min']) && strlen(trim($j['main']['temp_min'])) > 0 ) {
                                $result[$count]['temp_low'] = trim($j['main']['temp_min']);
                            }
                        }
                        $count++;
                    }
                    if ($count == 4) {
                        break;
                    }
                }
            }
        }
        return ($result);
    }
}
