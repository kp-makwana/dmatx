<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ContactMessageRequest;
use App\Models\ContactMessage;

class HomeController extends Controller
{

  public function dashboard()
  {
    $pageConfigs = ['myLayout' => 'horizontal'];

    return view('dashboard.dashboard',['pageConfigs'=> $pageConfigs]);
  }
  public function index()
  {
    $pageConfigs = ['myLayout' => 'front'];
    return view('frontend.index', ['pageConfigs' => $pageConfigs]);
  }

  public function contactUs(ContactMessageRequest $request)
  {
    $validated = $request->validated();

    ContactMessage::create([
      ...$validated,
      'ip_address' => $request->ip(),
    ]);

    return back()->with('success', 'Message sent successfully.');
  }
}
