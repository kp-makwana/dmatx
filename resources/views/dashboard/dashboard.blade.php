@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
  @vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/swiper/swiper.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/fonts/flag-icons.scss'
  ])
@endsection
@section('page-style')
  @vite('resources/assets/vendor/scss/pages/cards-advance.scss')
@endsection
@section('vendor-script')
  @vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/swiper/swiper.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
  ])
@endsection

@section('page-script')
  @vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
  <div class="row g-6">
    <!-- Website Analytics -->
    <div class="col-xl-6 col">
      <div class="swiper-container swiper swiper-card-advance-bg"
           id="swiper-with-pagination-cards">
        <div class="swiper-wrapper">

          <!-- ================= ZERODHA ================= -->
          <div class="swiper-slide">
            <div class="row">
              <div class="col-12">
                <h5 class="text-white mb-0">Demat Portfolio</h5>
                <small>Zerodha • Live Market</small>
              </div>

              <div class="row">
                <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1 pt-md-9">
                  <h6 class="text-white mb-4">Portfolio Overview</h6>

                  <div class="row">
                    <div class="col-6">
                      <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-4 align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">₹12.4L</p>
                          <p class="mb-0">Total Value</p>
                        </li>
                        <li class="d-flex align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">₹9.6L</p>
                          <p class="mb-0">Invested</p>
                        </li>
                      </ul>
                    </div>

                    <div class="col-6">
                      <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-4 align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                            +₹2.8L
                          </p>
                          <p class="mb-0">P&amp;L</p>
                        </li>
                        <li class="d-flex align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">18</p>
                          <p class="mb-0">Holdings</p>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 text-center my-4 my-md-0">
                  <img src="{{ asset('assets/img/illustrations/card-website-analytics-1.png') }}"
                       height="150"
                       class="card-website-analytics-img"
                       alt="Zerodha Portfolio">
                </div>
              </div>
            </div>
          </div>

          <!-- ================= GROWW ================= -->
          <div class="swiper-slide">
            <div class="row">
              <div class="col-12">
                <h5 class="text-white mb-0">Demat Portfolio</h5>
                <small>Groww • Live Market</small>
              </div>

              <div class="row">
                <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1 pt-md-9">
                  <h6 class="text-white mb-4">Investment Summary</h6>

                  <div class="row">
                    <div class="col-6">
                      <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-4 align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">₹6.2L</p>
                          <p class="mb-0">Total Value</p>
                        </li>
                        <li class="d-flex align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">₹5.9L</p>
                          <p class="mb-0">Invested</p>
                        </li>
                      </ul>
                    </div>

                    <div class="col-6">
                      <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-4 align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                            +₹30K
                          </p>
                          <p class="mb-0">P&amp;L</p>
                        </li>
                        <li class="d-flex align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">9</p>
                          <p class="mb-0">Holdings</p>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 text-center my-4 my-md-0">
                  <img src="{{ asset('assets/img/illustrations/card-website-analytics-2.png') }}"
                       height="150"
                       class="card-website-analytics-img"
                       alt="Groww Portfolio">
                </div>
              </div>
            </div>
          </div>

          <!-- ================= UPSTOX ================= -->
          <div class="swiper-slide">
            <div class="row">
              <div class="col-12">
                <h5 class="text-white mb-0">Demat Portfolio</h5>
                <small>Upstox • Live Market</small>
              </div>

              <div class="row">
                <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1 pt-md-9">
                  <h6 class="text-white mb-4">Holdings Breakdown</h6>

                  <div class="row">
                    <div class="col-6">
                      <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-4 align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">₹4.8L</p>
                          <p class="mb-0">Total Value</p>
                        </li>
                        <li class="d-flex align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">₹4.1L</p>
                          <p class="mb-0">Invested</p>
                        </li>
                      </ul>
                    </div>

                    <div class="col-6">
                      <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-4 align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                            +₹70K
                          </p>
                          <p class="mb-0">P&amp;L</p>
                        </li>
                        <li class="d-flex align-items-center">
                          <p class="mb-0 fw-medium me-2 website-analytics-text-bg">11</p>
                          <p class="mb-0">Holdings</p>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 text-center my-4 my-md-0">
                  <img src="{{ asset('assets/img/illustrations/card-website-analytics-3.png') }}"
                       height="150"
                       class="card-website-analytics-img"
                       alt="Upstox Portfolio">
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Pagination -->
        <div class="swiper-pagination"></div>
      </div>
    </div>

    <!--/ Website Analytics -->

    <!-- Average Daily Sales -->
    <div class="col-xl-3 col-sm-6">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h5 class="mb-3 card-title">Daily Portfolio Performance</h5>
          <p class="mb-0 text-body">Net P&amp;L Across All Demat Accounts</p>

          <div class="d-flex align-items-center gap-2">
            <h4 class="mb-0 text-success">+₹84,500</h4>
            <small class="text-success fw-medium">(+3.2%)</small>
          </div>
        </div>

        <div class="card-body px-0">
          <div id="averageDailySales"></div>
        </div>
      </div>
    </div>

    <!--/ Average Daily Sales -->

    <!-- Sales Overview -->
    <div class="col-xl-3 col-sm-6">
      <div class="card h-100">
        <div class="card-header">
          <div class="d-flex justify-content-between">
            <p class="mb-0 text-body">Investment Overview</p>
            <p class="card-text fw-medium text-success">+18.2%</p>
          </div>
          <h4 class="card-title mb-1">₹42.5L</h4>
        </div>

        <div class="card-body">
          <div class="row">
            <!-- PROFIT -->
            <div class="col-4">
              <div class="d-flex gap-2 align-items-center mb-2">
            <span class="badge bg-label-success p-1 rounded">
              <i class="icon-base ti tabler-trending-up icon-sm"></i>
            </span>
                <p class="mb-0">Profit</p>
              </div>
              <h5 class="mb-0 pt-1">62.2%</h5>
              <small class="text-body-secondary">₹6.44L</small>
            </div>

            <!-- VS DIVIDER -->
            <div class="col-4">
              <div class="divider divider-vertical">
                <div class="divider-text">
                  <span class="badge-divider-bg bg-label-secondary">VS</span>
                </div>
              </div>
            </div>

            <!-- INVESTED -->
            <div class="col-4 text-end">
              <div class="d-flex gap-2 justify-content-end align-items-center mb-2">
                <p class="mb-0">Invested</p>
                <span class="badge bg-label-primary p-1 rounded">
              <i class="icon-base ti tabler-wallet icon-sm"></i>
            </span>
              </div>
              <h5 class="mb-0 pt-1">25.5%</h5>
              <small class="text-body-secondary">₹12.7L</small>
            </div>
          </div>

          <!-- PROGRESS BAR -->
          <div class="d-flex align-items-center mt-6">
            <div class="progress w-100" style="height: 10px;">
              <div
                class="progress-bar bg-success"
                style="width: 62%"
                role="progressbar"
                aria-valuenow="62"
                aria-valuemin="0"
                aria-valuemax="100">
              </div>
              <div
                class="progress-bar bg-primary"
                style="width: 38%"
                role="progressbar"
                aria-valuenow="38"
                aria-valuemin="0"
                aria-valuemax="100">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--/ Sales Overview -->

    <!-- Portfolio Earnings Reports -->
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header pb-0 d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">Portfolio Earnings</h5>
            <p class="card-subtitle">Weekly P&amp;L Overview</p>
          </div>

          <div class="dropdown">
            <button
              class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1"
              type="button"
              id="earningReportsId"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false">
              <i class="icon-base ti tabler-dots-vertical icon-md text-body-secondary"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsId">
              <a class="dropdown-item" href="javascript:void(0);">View Details</a>
              <a class="dropdown-item" href="javascript:void(0);">Download Report</a>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row align-items-center g-md-8">
            <!-- SUMMARY -->
            <div class="col-12 col-md-5 d-flex flex-column">
              <div class="d-flex gap-2 align-items-center mb-3 flex-wrap">
                <h2 class="mb-0 text-success">+₹468</h2>
                <div class="badge rounded bg-label-success">+4.2%</div>
              </div>
              <small class="text-body">
                Performance improved compared to last trading week
              </small>
            </div>

            <!-- CHART -->
            <div class="col-12 col-md-7 ps-xl-8">
              <div id="weeklyEarningReports"></div>
            </div>
          </div>

          <!-- BREAKDOWN -->
          <div class="border rounded p-5 mt-5">
            <div class="row gap-4 gap-sm-0">

              <!-- TOTAL RETURNS -->
              <div class="col-12 col-sm-4">
                <div class="d-flex gap-2 align-items-center">
                  <div class="badge rounded bg-label-primary p-1">
                    <i class="icon-base ti tabler-currency-rupee icon-18px"></i>
                  </div>
                  <h6 class="mb-0 fw-normal">Total Returns</h6>
                </div>
                <h4 class="my-2">₹545.69</h4>
                <div class="progress w-75" style="height:4px">
                  <div
                    class="progress-bar"
                    role="progressbar"
                    style="width: 65%"
                    aria-valuenow="65"
                    aria-valuemin="0"
                    aria-valuemax="100">
                  </div>
                </div>
              </div>

              <!-- PROFIT -->
              <div class="col-12 col-sm-4">
                <div class="d-flex gap-2 align-items-center">
                  <div class="badge rounded bg-label-success p-1">
                    <i class="icon-base ti tabler-trending-up icon-18px"></i>
                  </div>
                  <h6 class="mb-0 fw-normal">Profit</h6>
                </div>
                <h4 class="my-2">₹256.34</h4>
                <div class="progress w-75" style="height:4px">
                  <div
                    class="progress-bar bg-success"
                    role="progressbar"
                    style="width: 50%"
                    aria-valuenow="50"
                    aria-valuemin="0"
                    aria-valuemax="100">
                  </div>
                </div>
              </div>

              <!-- CHARGES / LOSSES -->
              <div class="col-12 col-sm-4">
                <div class="d-flex gap-2 align-items-center">
                  <div class="badge rounded bg-label-danger p-1">
                    <i class="icon-base ti tabler-receipt icon-18px"></i>
                  </div>
                  <h6 class="mb-0 fw-normal">Charges &amp; Losses</h6>
                </div>
                <h4 class="my-2">₹74.19</h4>
                <div class="progress w-75" style="height:4px">
                  <div
                    class="progress-bar bg-danger"
                    role="progressbar"
                    style="width: 35%"
                    aria-valuenow="35"
                    aria-valuemin="0"
                    aria-valuemax="100">
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Support Tracker -->
    <div class="col-12 col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">Portfolio Activity</h5>
            <p class="card-subtitle">Last 7 Trading Days</p>
          </div>
          <div class="dropdown">
            <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-2 me-n1" type="button"
                    id="supportTrackerMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base ti tabler-dots-vertical icon-md text-body-secondary"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="supportTrackerMenu">
              <a class="dropdown-item" href="javascript:void(0);">View Details</a>
              <a class="dropdown-item" href="javascript:void(0);">Export Data</a>
            </div>
          </div>
        </div>

        <div class="card-body row">
          <div class="col-12 col-sm-4">
            <div class="mt-lg-4 mt-lg-2 mb-lg-6 mb-2">
              <h2 class="mb-0">164</h2>
              <p class="mb-0">Total Trades</p>
            </div>

            <ul class="p-0 m-0">
              <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                <div class="badge rounded bg-label-primary p-1_5">
                  <i class="icon-base ti tabler-ticket icon-md"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-nowrap">Executed Trades</h6>
                  <small class="text-body-secondary">142</small>
                </div>
              </li>

              <li class="d-flex gap-4 align-items-center mb-lg-3 pb-1">
                <div class="badge rounded bg-label-info p-1_5">
                  <i class="icon-base ti tabler-circle-check icon-md"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-nowrap">Open Positions</h6>
                  <small class="text-body-secondary">28</small>
                </div>
              </li>

              <li class="d-flex gap-4 align-items-center pb-1">
                <div class="badge rounded bg-label-warning p-1_5">
                  <i class="icon-base ti tabler-clock icon-md"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-nowrap">Avg Holding Time</h6>
                  <small class="text-body-secondary">1 Day</small>
                </div>
              </li>
            </ul>
          </div>

          <div class="col-12 col-md-8">
            <div id="supportTracker"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
