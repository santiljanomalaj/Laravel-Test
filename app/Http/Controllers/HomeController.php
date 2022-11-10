<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

use App\Models\FetchData;
use App\Models\Poster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MongoDB\Driver\Session;

use Storage;

class HomeController extends Controller
{


    public function index()
    {
       //
    }

    public function fetch(Request $request){
        $id = $request['id'];
        $fetchUrl = '';
        switch ($id) {
            case '1':
                $fetchUrl = 'http://www.omdbapi.com/?s=Matrix&apikey=720c3666';
                break;
            case '2':
                $fetchUrl = 'http://www.omdbapi.com/?s=Matrix%20Reloaded&apikey=720c3666';
                break;
            case '3':
                $fetchUrl = 'http://www.omdbapi.com/?s=Matrix%20Revolutions&apikey=720c3666';
                break;
            
            default:
                # code...
                break;
        }
        $response = Http::get($fetchUrl);
        foreach ($response['Search'] as $key => $value) {
            $posterId = null;
            if($value['Poster'] !== 'N/A'){
                $getPoster = Poster::select('poster')->where('poster', $value['Poster'])->get();
                if(count($getPoster) == 0){
                    
                    $poster = new Poster;
                    $poster->poster = $value['Poster'];
                    $poster->save();
                    $posterId = $poster['id'];
                    
                } else {
                    $posterId = $getPoster[0]['id'].'000-000';
                }
            }
            $getFetchData = FetchData::select('imdbID')->where('imdbID', $value['imdbID'])->get();
            if(count($getFetchData) == 0){
                $fetchData = new FetchData;
                $fetchData->title = $value['Title'];
                $fetchData->year = $value['Year'];
                $fetchData->imdbID = $value['imdbID'];
                $fetchData->type = $value['Type'];
                $fetchData->poster = $posterId;
                $fetchData->save();
            }
        }
        return $response;
    }

   
}
