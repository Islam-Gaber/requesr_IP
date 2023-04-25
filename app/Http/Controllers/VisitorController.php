<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    public function logVisit(Request $request) // Define a public method named logVisit that takes in a Request object
    {
        $visitor = DB::table('visitors')->where([ // Query the visitors table using the DB class
            ['ip_address', '=', $request->ip()], // Select records where the 'ip_address' column equals the visitor's IP address
            ['page_visited', '=', $request->path()] // Select records where the 'page_visited' column equals the current page's path
        ])->first(); // Retrieve the first matching record

        if (!$visitor) { // Check if there is no existing visitor record for the current IP address and page visited
            DB::table('visitors')->insert([ // Insert a new visitor record using the DB class
                'ip_address' => $request->ip(), // Set the 'ip_address' column to the visitor's IP address
                'user_agent' => $request->header('User-Agent'), // Set the 'user_agent' column to the visitor's user agent string
                'page_visited' => $request->path(), // Set the 'page_visited' column to the current page's path
                'created_at' => now(), // Set the 'created_at' column to the current datetime
                'updated_at' => now() // Set the 'updated_at' column to the current datetime
            ]);
        }

        $visitorCount = DB::table('visitors')->where('page_visited', $request->path())->distinct('ip_address')->count('ip_address'); // Query the visitors table to count the number of unique visitors for the current page

        return view('welcome', ['visitorCount' => $visitorCount]); // Return a view named 'welcome' with the visitor count passed as a variable named 'visitorCount'
    }
}
