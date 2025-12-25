<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AngleOneAccount extends Controller
{
  public function createStepOne()
  {
    $pageConfigs = ['myLayout' => 'horizontal'];
    return view('angle-one-account.create-step-one',compact('pageConfigs'));
  }
}
