@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Home')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/nouislider/nouislider.scss', 'resources/assets/vendor/libs/swiper/swiper.scss'])
@endsection

<!-- Page Styles -->
@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/nouislider/nouislider.js', 'resources/assets/vendor/libs/swiper/swiper.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite(['resources/assets/js/front-page-landing.js'])
@endsection


@section('content')
  <div data-bs-spy="scroll" class="scrollspy-example">
    <!-- Hero: Start -->
    <section id="hero-animation">
      <div id="landingHero" class="section-py landing-hero position-relative">
        <img
          src="{{ asset('assets/img/front-pages/backgrounds/hero-bg.png') }}"
          alt="hero background"
          class="position-absolute top-0 start-50 translate-middle-x object-fit-cover w-100 h-100"
          data-speed="1"
        />

        <div class="container">
          <div class="hero-text-box text-center position-relative">
            <h1 class="text-primary hero-title display-6 fw-extrabold">
              One Platform to Manage All Your Demat Accounts
            </h1>

            <h2 class="hero-sub-title h6 mb-6">
              Consolidate, track, and analyze multiple Demat accounts<br class="d-none d-lg-block" />
              across brokers using a single secure dashboard.
            </h2>

            <div class="landing-hero-btn d-inline-block position-relative">
          <span class="hero-btn-item position-absolute d-none d-md-flex fw-medium">
            Built for modern investors
            <img
              src="{{ asset('assets/img/front-pages/icons/Join-community-arrow.png') }}"
              alt="arrow"
              class="scaleX-n1-rtl"
            />
          </span>

              <button class="btn btn-primary btn-lg" type="button">
                Request Early Access
              </button>
            </div>
          </div>

          <div id="heroDashboardAnimation" class="hero-animation-img">
            <div id="heroAnimationImg" class="position-relative hero-dashboard-img">
              <img
                src="{{ asset('assets/img/front-pages/landing-page/hero-dashboard-' . $configData['theme'] . '.png') }}"
                alt="platform dashboard preview"
                class="animation-img"
                data-app-light-img="front-pages/landing-page/hero-dashboard-light.png"
                data-app-dark-img="front-pages/landing-page/hero-dashboard-dark.png"
              />

              <img
                src="{{ asset('assets/img/front-pages/landing-page/hero-elements-' . $configData['theme'] . '.png') }}"
                alt="dashboard elements"
                class="position-absolute hero-elements-img animation-img top-0 start-0"
                data-app-light-img="front-pages/landing-page/hero-elements-light.png"
                data-app-dark-img="front-pages/landing-page/hero-elements-dark.png"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="landing-hero-blank"></div>
    </section>
    <!-- Hero: End -->

    <!-- Useful features: Start -->
    <section id="landingFeatures" class="section-py landing-features">
      <div class="container">
        <div class="text-center mb-4">
          <span class="badge bg-label-primary">Platform Features</span>
        </div>

        <h4 class="text-center mb-1">
      <span class="position-relative fw-extrabold z-1">
        Everything you need
        <img
          src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}"
          alt="section icon"
          class="section-title-img position-absolute object-fit-contain bottom-0 z-n1"
        />
      </span>
          to manage multiple Demat accounts
        </h4>

        <p class="text-center mb-12">
          A unified platform built for investors who want complete visibility and control.
        </p>

        <div class="features-icon-wrapper row gx-0 gy-6 g-sm-12">

          <!-- Feature 1 -->
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="mb-4 text-primary text-center">
              <!-- Portfolio / Dashboard Icon -->
              <svg width="64" height="65" viewBox="0 0 64 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="8" y="12" width="48" height="36" rx="4" fill="currentColor" opacity="0.2"/>
                <rect x="14" y="20" width="14" height="20" rx="2" fill="currentColor"/>
                <rect x="32" y="24" width="18" height="16" rx="2" fill="currentColor"/>
              </svg>
            </div>
            <h5 class="mb-2">Unified Portfolio Dashboard</h5>
            <p class="features-icon-description">
              View holdings from all Demat accounts in a single consolidated dashboard.
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="mb-4 text-primary text-center">
              <!-- Refresh / Sync Icon -->
              <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32 10C19.85 10 10 19.85 10 32C10 44.15 19.85 54 32 54"
                      stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                <path d="M32 54C44.15 54 54 44.15 54 32C54 19.85 44.15 10 32 10"
                      stroke="currentColor" stroke-width="4" stroke-linecap="round" opacity="0.3"/>
              </svg>
            </div>
            <h5 class="mb-2">Real-Time Account Sync</h5>
            <p class="features-icon-description">
              Automatically sync holdings, balances, and transactions across brokers.
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="text-center mb-4 text-primary">
              <!-- Layers / Consolidation Icon -->
              <svg width="64" height="65" viewBox="0 0 64 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32 10L8 22L32 34L56 22L32 10Z" fill="currentColor"/>
                <path d="M8 30L32 42L56 30" stroke="currentColor" stroke-width="4"/>
                <path d="M8 38L32 50L56 38" stroke="currentColor" stroke-width="4" opacity="0.4"/>
              </svg>
            </div>
            <h5 class="mb-2">Consolidated Investment View</h5>
            <p class="features-icon-description">
              See total exposure, asset allocation, and performance across accounts.
            </p>
          </div>

          <!-- Feature 4 -->
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="text-center mb-4 text-primary">
              <!-- Shield / Security Icon -->
              <svg width="64" height="65" viewBox="0 0 64 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32 8L12 16V30C12 43 22 51 32 56C42 51 52 43 52 30V16L32 8Z"
                      fill="currentColor" opacity="0.2"/>
                <path d="M24 32L30 38L40 26" stroke="currentColor" stroke-width="4"
                      stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h5 class="mb-2">Secure Data Protection</h5>
            <p class="features-icon-description">
              Bank-grade encryption and secure integrations designed for financial data.
            </p>
          </div>

          <!-- Feature 5 -->
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="text-center mb-4 text-primary">
              <!-- User / Support Icon -->
              <svg width="64" height="65" viewBox="0 0 64 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="32" cy="22" r="10" fill="currentColor"/>
                <path d="M14 52C14 42 22 36 32 36C42 36 50 42 50 52"
                      stroke="currentColor" stroke-width="4" opacity="0.6"/>
              </svg>
            </div>
            <h5 class="mb-2">Investor-Focused Support</h5>
            <p class="features-icon-description">
              Dedicated assistance for onboarding, brokers, and portfolio questions.
            </p>
          </div>

          <!-- Feature 6 -->
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="text-center mb-4 text-primary">
              <!-- Report / Document Icon -->
              <svg width="64" height="65" viewBox="0 0 64 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="16" y="10" width="32" height="44" rx="4"
                      fill="currentColor" opacity="0.2"/>
                <line x1="22" y1="24" x2="42" y2="24"
                      stroke="currentColor" stroke-width="3"/>
                <line x1="22" y1="32" x2="42" y2="32"
                      stroke="currentColor" stroke-width="3"/>
                <line x1="22" y1="40" x2="36" y2="40"
                      stroke="currentColor" stroke-width="3"/>
              </svg>
            </div>
            <h5 class="mb-2">Transparent Reporting</h5>
            <p class="features-icon-description">
              Clear reports for holdings, transactions, and historical performance.
            </p>
          </div>

        </div>
      </div>
    </section>
    <!-- Useful features: End -->

    <!-- Real customers reviews: Start -->
    <section id="landingReviews" class="section-py bg-body landing-reviews pb-0">
      <!-- What people say slider: Start -->
      <div class="container">
        <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5">
          <div class="col-md-6 col-lg-5 col-xl-3">
            <div class="mb-4">
              <span class="badge bg-label-primary">Investor Testimonials</span>
            </div>
            <h4 class="mb-1">
          <span class="position-relative fw-extrabold z-1">
            What investors say
            <img
              src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}"
              alt="section icon"
              class="section-title-img position-absolute object-fit-contain bottom-0 z-n1"
            />
          </span>
            </h4>
            <p class="mb-5 mb-md-12">
              Real feedback from investors managing<br class="d-none d-xl-block" />
              multiple Demat accounts.
            </p>
            <div class="landing-reviews-btns">
              <button id="reviews-previous-btn" class="btn btn-icon btn-label-primary reviews-btn me-3" type="button">
                <i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i>
              </button>
              <button id="reviews-next-btn" class="btn btn-icon btn-label-primary reviews-btn" type="button">
                <i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i>
              </button>
            </div>
          </div>

          <div class="col-md-6 col-lg-7 col-xl-9">
            <div class="swiper-reviews-carousel overflow-hidden">
              <div class="swiper" id="swiper-reviews">
                <div class="swiper-wrapper">

                  <!-- Review 1 -->
                  <div class="swiper-slide">
                    <div class="card h-100">
                      <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                        <div class="mb-4">
                          <img src="{{ asset('assets/img/front-pages/branding/logo-1.png') }}" alt="client logo"
                               class="client-logo img-fluid" />
                        </div>
                        <p>
                          “Managing multiple Demat accounts was always confusing.
                          This platform finally gives me a clear, consolidated view.”
                        </p>
                        <div class="text-warning mb-4">
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="avatar me-3 avatar-sm">
                            <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div>
                            <h6 class="mb-0">Amit Verma</h6>
                            <p class="small text-body-secondary mb-0">Retail Investor</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Review 2 -->
                  <div class="swiper-slide">
                    <div class="card h-100">
                      <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                        <div class="mb-4">
                          <img src="{{ asset('assets/img/front-pages/branding/logo-2.png') }}" alt="client logo"
                               class="client-logo img-fluid" />
                        </div>
                        <p>
                          “I track investments across different brokers.
                          Seeing everything in one dashboard saves me hours every month.”
                        </p>
                        <div class="text-warning mb-4">
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="avatar me-3 avatar-sm">
                            <img src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div>
                            <h6 class="mb-0">Neha Kapoor</h6>
                            <p class="small text-body-secondary mb-0">Active Trader</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Review 3 -->
                  <div class="swiper-slide">
                    <div class="card h-100">
                      <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                        <div class="mb-4">
                          <img src="{{ asset('assets/img/front-pages/branding/logo-3.png') }}" alt="client logo"
                               class="client-logo img-fluid" />
                        </div>
                        <p>
                          “The consolidated reports make tax planning and performance
                          review much easier for my clients.”
                        </p>
                        <div class="text-warning mb-4">
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="avatar me-3 avatar-sm">
                            <img src="{{ asset('assets/img/avatars/3.png') }}" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div>
                            <h6 class="mb-0">Rahul Mehta</h6>
                            <p class="small text-body-secondary mb-0">Investment Advisor</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Review 4 -->
                  <div class="swiper-slide">
                    <div class="card h-100">
                      <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                        <div class="mb-4">
                          <img src="{{ asset('assets/img/front-pages/branding/logo-4.png') }}" alt="client logo"
                               class="client-logo img-fluid" />
                        </div>
                        <p>
                          “Clean interface, accurate data, and strong security.
                          Exactly what I expect from a financial platform.”
                        </p>
                        <div class="text-warning mb-4">
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                          <i class="icon-base ti tabler-star-filled"></i>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="avatar me-3 avatar-sm">
                            <img src="{{ asset('assets/img/avatars/4.png') }}" alt="Avatar" class="rounded-circle" />
                          </div>
                          <div>
                            <h6 class="mb-0">Sonal Iyer</h6>
                            <p class="small text-body-secondary mb-0">Long-Term Investor</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <hr class="m-0 mt-6 mt-md-12" />

      <!-- Logo slider: Start (unchanged) -->
      <div class="container">
        <div class="swiper-logo-carousel pt-8">
          <div class="swiper" id="swiper-clients-logos">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <img src="{{ asset('assets/img/front-pages/branding/logo_1-' . $configData['theme'] . '.png') }}"
                     alt="client logo" class="client-logo"
                     data-app-light-img="front-pages/branding/logo_1-light.png"
                     data-app-dark-img="front-pages/branding/logo_1-dark.png" />
              </div>
              <div class="swiper-slide">
                <img src="{{ asset('assets/img/front-pages/branding/logo_2-' . $configData['theme'] . '.png') }}"
                     alt="client logo" class="client-logo"
                     data-app-light-img="front-pages/branding/logo_2-light.png"
                     data-app-dark-img="front-pages/branding/logo_2-dark.png" />
              </div>
              <div class="swiper-slide">
                <img src="{{ asset('assets/img/front-pages/branding/logo_3-' . $configData['theme'] . '.png') }}"
                     alt="client logo" class="client-logo"
                     data-app-light-img="front-pages/branding/logo_3-light.png"
                     data-app-dark-img="front-pages/branding/logo_3-dark.png" />
              </div>
              <div class="swiper-slide">
                <img src="{{ asset('assets/img/front-pages/branding/logo_4-' . $configData['theme'] . '.png') }}"
                     alt="client logo" class="client-logo"
                     data-app-light-img="front-pages/branding/logo_4-light.png"
                     data-app-dark-img="front-pages/branding/logo_4-dark.png" />
              </div>
              <div class="swiper-slide">
                <img src="{{ asset('assets/img/front-pages/branding/logo_5-' . $configData['theme'] . '.png') }}"
                     alt="client logo" class="client-logo"
                     data-app-light-img="front-pages/branding/logo_5-light.png"
                     data-app-dark-img="front-pages/branding/logo_5-dark.png" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Logo slider: End -->
    </section>
    <!-- Real customers reviews: End -->

    <!-- Our great team: Start -->
    <section id="landingTeam" class="section-py landing-team">
      <div class="container">
        <div class="text-center mb-4">
          <span class="badge bg-label-primary">Our Team</span>
        </div>

        <h4 class="text-center mb-1">
      <span class="position-relative fw-extrabold z-1">
        Built & Supported
        <img
          src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}"
          alt="section icon"
          class="section-title-img position-absolute object-fit-contain bottom-0 z-n1"
        />
      </span>
          by Industry Experts
        </h4>

        <p class="text-center mb-md-11 pb-0 pb-xl-12">
          A team of professionals focused on building reliable financial technology.
        </p>

        <div class="row gy-12 mt-2">

          <!-- Team Member 1 -->
          <div class="col-lg-3 col-sm-6">
            <div class="card mt-3 mt-lg-0 shadow-none">
              <div class="bg-label-primary border border-bottom-0 border-label-primary position-relative team-image-box">
                <img
                  src="{{ asset('assets/img/front-pages/landing-page/team-member-1.png') }}"
                  class="position-absolute card-img-position bottom-0 start-50"
                  alt="team member"
                />
              </div>
              <div class="card-body border border-top-0 border-label-primary text-center">
                <h5 class="card-title mb-0">Sophie Gilbert</h5>
                <p class="text-body-secondary mb-0">Product & Strategy Lead</p>
              </div>
            </div>
          </div>

          <!-- Team Member 2 -->
          <div class="col-lg-3 col-sm-6">
            <div class="card mt-3 mt-lg-0 shadow-none">
              <div class="bg-label-info border border-bottom-0 border-label-info position-relative team-image-box">
                <img
                  src="{{ asset('assets/img/front-pages/landing-page/team-member-2.png') }}"
                  class="position-absolute card-img-position bottom-0 start-50"
                  alt="team member"
                />
              </div>
              <div class="card-body border border-top-0 border-label-info text-center">
                <h5 class="card-title mb-0">Paul Miles</h5>
                <p class="text-body-secondary mb-0">UI / UX Designer</p>
              </div>
            </div>
          </div>

          <!-- Team Member 3 -->
          <div class="col-lg-3 col-sm-6">
            <div class="card mt-3 mt-lg-0 shadow-none">
              <div class="bg-label-danger border border-bottom-0 border-label-danger position-relative team-image-box">
                <img
                  src="{{ asset('assets/img/front-pages/landing-page/team-member-3.png') }}"
                  class="position-absolute card-img-position bottom-0 start-50"
                  alt="team member"
                />
              </div>
              <div class="card-body border border-top-0 border-label-danger text-center">
                <h5 class="card-title mb-0">Nannie Ford</h5>
                <p class="text-body-secondary mb-0">Engineering Lead</p>
              </div>
            </div>
          </div>

          <!-- Team Member 4 -->
          <div class="col-lg-3 col-sm-6">
            <div class="card mt-3 mt-lg-0 shadow-none">
              <div class="bg-label-success border border-bottom-0 border-label-success position-relative team-image-box">
                <img
                  src="{{ asset('assets/img/front-pages/landing-page/team-member-4.png') }}"
                  class="position-absolute card-img-position bottom-0 start-50"
                  alt="team member"
                />
              </div>
              <div class="card-body border border-top-0 border-label-success text-center">
                <h5 class="card-title mb-0">Chris Watkins</h5>
                <p class="text-body-secondary mb-0">Customer Success & Growth</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
    <!-- Our great team: End -->

    <!-- Pricing plans: Start -->
    <section id="landingPricing" class="section-py bg-body landing-pricing">
      <div class="container">
        <div class="text-center mb-4">
          <span class="badge bg-label-primary">Pricing Plans</span>
        </div>
        <h4 class="text-center mb-1">
        <span class="position-relative fw-extrabold z-1">Tailored pricing plans
          <img src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}" alt="laptop charging"
               class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
        </span>
          designed for you
        </h4>
        <p class="text-center pb-2 mb-7">All plans include 40+ advanced tools and features to boost your
          product.<br />Choose the best plan to fit your needs.</p>
        <div class="text-center mb-12">
          <div class="position-relative d-inline-block pt-3 pt-md-0">
            <label class="switch switch-sm switch-primary me-0">
              <span class="switch-label fs-6 text-body me-3">Pay Monthly</span>
              <input type="checkbox" class="switch-input price-duration-toggler" checked />
              <span class="switch-toggle-slider">
              <span class="switch-on"></span>
              <span class="switch-off"></span>
            </span>
              <span class="switch-label fs-6 text-body ms-3">Pay Annual</span>
            </label>
            <div class="pricing-plans-item position-absolute d-flex">
              <img src="{{ asset('assets/img/front-pages/icons/pricing-plans-arrow.png') }}" alt="pricing plans arrow"
                   class="scaleX-n1-rtl" />
              <span class="fw-medium mt-2 ms-1"> Save 25%</span>
            </div>
          </div>
        </div>
        <div class="row g-6 pt-lg-5">
          <!-- Basic Plan: Start -->
          <div class="col-xl-4 col-lg-6">
            <div class="card">
              <div class="card-header">
                <div class="text-center">
                  <img src="{{ asset('assets/img/front-pages/icons/paper-airplane.png') }}" alt="paper airplane icon"
                       class="mb-8 pb-2" />
                  <h4 class="mb-0">Basic</h4>
                  <div class="d-flex align-items-center justify-content-center">
                    <span class="price-monthly h2 text-primary fw-extrabold mb-0">$19</span>
                    <span class="price-yearly h2 text-primary fw-extrabold mb-0 d-none">$14</span>
                    <sub class="h6 text-body-secondary mb-n1 ms-1">/mo</sub>
                  </div>
                  <div class="position-relative pt-2">
                    <div class="price-yearly text-body-secondary price-yearly-toggle d-none">$ 168 / year</div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <ul class="list-unstyled pricing-list">
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Timeline
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Basic search
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Live chat widget
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Email marketing
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Custom Forms
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Traffic analytics
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Basic Support
                    </h6>
                  </li>
                </ul>
                <div class="d-grid mt-8">
                  <a href="{{ url('/front-pages/payment') }}" class="btn btn-label-primary">Get Started</a>
                </div>
              </div>
            </div>
          </div>
          <!-- Basic Plan: End -->

          <!-- Favourite Plan: Start -->
          <div class="col-xl-4 col-lg-6">
            <div class="card border border-primary shadow-xl">
              <div class="card-header">
                <div class="text-center">
                  <img src="{{ asset('assets/img/front-pages/icons/plane.png') }}" alt="plane icon" class="mb-8 pb-2" />
                  <h4 class="mb-0">Team</h4>
                  <div class="d-flex align-items-center justify-content-center">
                    <span class="price-monthly h2 text-primary fw-extrabold mb-0">$29</span>
                    <span class="price-yearly h2 text-primary fw-extrabold mb-0 d-none">$22</span>
                    <sub class="h6 text-body-secondary mb-n1 ms-1">/mo</sub>
                  </div>
                  <div class="position-relative pt-2">
                    <div class="price-yearly text-body-secondary price-yearly-toggle d-none">$ 264 / year</div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <ul class="list-unstyled pricing-list">
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Everything in basic
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Timeline with database
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Advanced search
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Marketing automation
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Advanced chatbot
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Campaign management
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Collaboration tools
                    </h6>
                  </li>
                </ul>
                <div class="d-grid mt-8">
                  <a href="{{ url('/front-pages/payment') }}" class="btn btn-primary">Get Started</a>
                </div>
              </div>
            </div>
          </div>
          <!-- Favourite Plan: End -->

          <!-- Standard Plan: Start -->
          <div class="col-xl-4 col-lg-6">
            <div class="card">
              <div class="card-header">
                <div class="text-center">
                  <img src="{{ asset('assets/img/front-pages/icons/shuttle-rocket.png') }}" alt="shuttle rocket icon"
                       class="mb-8 pb-2" />
                  <h4 class="mb-0">Enterprise</h4>
                  <div class="d-flex align-items-center justify-content-center">
                    <span class="price-monthly h2 text-primary fw-extrabold mb-0">$49</span>
                    <span class="price-yearly h2 text-primary fw-extrabold mb-0 d-none">$37</span>
                    <sub class="h6 text-body-secondary mb-n1 ms-1">/mo</sub>
                  </div>
                  <div class="position-relative pt-2">
                    <div class="price-yearly text-body-secondary price-yearly-toggle d-none">$ 444 / year</div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <ul class="list-unstyled pricing-list">
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Everything in premium
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Timeline with database
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Fuzzy search
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      A/B testing sanbox
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Custom permissions
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Social media automation
                    </h6>
                  </li>
                  <li>
                    <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill bg-label-primary p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                      Sales automation tools
                    </h6>
                  </li>
                </ul>
                <div class="d-grid mt-8">
                  <a href="{{ url('/front-pages/payment') }}" class="btn btn-label-primary">Get Started</a>
                </div>
              </div>
            </div>
          </div>
          <!-- Standard Plan: End -->
        </div>
      </div>
    </section>
    <!-- Pricing plans: End -->

    <!-- Fun facts: Start -->
    <section id="landingFunFacts" class="section-py landing-fun-facts">
      <div class="container">
        <div class="row gy-6">

          <!-- Demat Accounts -->
          <div class="col-sm-6 col-lg-3">
            <div class="card border border-primary shadow-none">
              <div class="card-body text-center">
                <div class="mb-4 text-primary">
                  <!-- icon unchanged -->
                  <svg width="64" height="65" viewBox="0 0 64 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.2"
                          d="M10 44.4663V18.4663C10 17.4054 10.4214 16.388 11.1716 15.6379C11.9217 14.8877 12.9391 14.4663 14 14.4663H50C51.0609 14.4663 52.0783 14.8877 52.8284 15.6379C53.5786 16.388 54 17.4054 54 18.4663V44.4663H10Z"
                          fill="currentColor" />
                    <path
                      d="M10 44.4663V18.4663C10 17.4054 10.4214 16.388 11.1716 15.6379C11.9217 14.8877 12.9391 14.4663 14 14.4663H50C51.0609 14.4663 52.0783 14.8877 52.8284 15.6379C53.5786 16.388 54 17.4054 54 18.4663V44.4663M36 22.4663H28M6 44.4663H58V48.4663C58 49.5272 57.5786 50.5446 56.8284 51.2947C56.0783 52.0449 55.0609 52.4663 54 52.4663H10C8.93913 52.4663 7.92172 52.0449 7.17157 51.2947C6.42143 50.5446 6 49.5272 6 48.4663V44.4663Z"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </div>
                <h3 class="mb-0">25k+</h3>
                <p class="fw-medium mb-0">
                  Demat Accounts<br />
                  Managed
                </p>
              </div>
            </div>
          </div>

          <!-- Active Users -->
          <div class="col-sm-6 col-lg-3">
            <div class="card border border-success shadow-none">
              <div class="card-body text-center">
                <div class="mb-4 text-success">
                  <!-- icon unchanged -->
                  <svg width="65" height="65" viewBox="0 0 65 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.2"
                          d="M32.5 8.52881C27.6437 8.52739 22.9012 9.99922 18.899 12.7499C14.8969 15.5005 11.8233 19.4006 10.0844 23.9348C8.34542 28.4691 8.02291 33.4242 9.15945 38.1456C10.296 42.867 12.8381 47.1326 16.4499 50.3788Z"
                          fill="currentColor" />
                    <path
                      d="M56.5 32.5288C56.5 45.7836 45.7548 56.5288 32.5 56.5288C19.2452 56.5288 8.5 45.7836 8.5 32.5288C8.5 19.274 19.2452 8.52881 32.5 8.52881C45.7548 8.52881 56.5 19.274 56.5 32.5288Z"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </div>
                <h3 class="mb-0">12k+</h3>
                <p class="fw-medium mb-0">
                  Active Investors<br />
                  & Advisors
                </p>
              </div>
            </div>
          </div>

          <!-- Platform Rating -->
          <div class="col-sm-6 col-lg-3">
            <div class="card border border-info shadow-none">
              <div class="card-body text-center">
                <div class="mb-4 text-info">
                  <!-- icon unchanged -->
                  <svg width="65" height="65" viewBox="0 0 65 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.2"
                          d="M46.5 10.5288H32.5L20.225 26.5288L32.5 56.5288L60.5 26.5288L46.5 10.5288Z"
                          fill="currentColor" />
                    <path d="M18.5 10.5288H46.5L60.5 26.5288L32.5 56.5288L4.5 26.5288L18.5 10.5288Z"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </div>
                <h3 class="mb-0">4.9/5</h3>
                <p class="fw-medium mb-0">
                  User Satisfaction<br />
                  Rating
                </p>
              </div>
            </div>
          </div>

          <!-- Data Security -->
          <div class="col-sm-6 col-lg-3">
            <div class="card border border-warning shadow-none">
              <div class="card-body text-center">
                <div class="mb-4 text-warning">
                  <!-- icon unchanged -->
                  <svg width="65" height="65" viewBox="0 0 65 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.2"
                          d="M14.125 50.9038C11.825 48.6038 13.35 43.7788 12.175 40.9538C11 38.1288 6.5 35.6538 6.5 32.5288Z"
                          fill="currentColor" />
                    <path
                      d="M43.5 26.5288L28.825 40.5288L21.5 33.5288"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </div>
                <h3 class="mb-0">100%</h3>
                <p class="fw-medium mb-0">
                  Secure & Read-Only<br />
                  Access
                </p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
    <!-- Fun facts: End -->

    <!-- FAQ: Start -->
    <section id="landingFAQ" class="section-py bg-body landing-faq">
      <div class="container">

        <div class="text-center mb-4">
          <span class="badge bg-label-primary">FAQ</span>
        </div>

        <h4 class="text-center mb-1">
          Frequently asked
          <span class="position-relative fw-extrabold z-1">
        questions
        <img
          src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}"
          alt="section title"
          class="section-title-img position-absolute object-fit-contain bottom-0 z-n1"
        />
      </span>
        </h4>

        <p class="text-center mb-12 pb-md-4">
          Everything you need to know about managing multiple Demat accounts on one platform.
        </p>

        <div class="row gy-12 align-items-center">
          <div class="col-lg-5">
            <div class="text-center">
              <img
                src="{{ asset('assets/img/front-pages/landing-page/faq-boy-with-logos.png') }}"
                alt="FAQ illustration"
                class="faq-image"
              />
            </div>
          </div>

          <div class="col-lg-7">
            <div class="accordion" id="accordionExample">

              <!-- FAQ 1 -->
              <div class="card accordion-item">
                <h2 class="accordion-header" id="headingOne">
                  <button
                    type="button"
                    class="accordion-button"
                    data-bs-toggle="collapse"
                    data-bs-target="#accordionOne"
                    aria-expanded="true"
                    aria-controls="accordionOne">
                    Is my Demat account data safe?
                  </button>
                </h2>

                <div id="accordionOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    Yes. We use <strong>read-only access</strong> provided by your broker. This means we can only
                    fetch portfolio data — we <strong>cannot place trades, transfer funds, or modify holdings</strong>.
                    Your login credentials are never stored on our servers.
                  </div>
                </div>
              </div>

              <!-- FAQ 2 -->
              <div class="card accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                  <button
                    type="button"
                    class="accordion-button collapsed"
                    data-bs-toggle="collapse"
                    data-bs-target="#accordionTwo"
                    aria-expanded="false"
                    aria-controls="accordionTwo">
                    How many Demat accounts can I add?
                  </button>
                </h2>

                <div id="accordionTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    The number of Demat accounts depends on your plan:
                    <ul class="mt-2">
                      <li><strong>Retail:</strong> Up to 10 accounts</li>
                      <li><strong>Pro Investor:</strong> Up to 50 accounts</li>
                      <li><strong>Advisor:</strong> Unlimited accounts</li>
                    </ul>
                    You can upgrade your plan anytime as your needs grow.
                  </div>
                </div>
              </div>

              <!-- FAQ 3 -->
              <div class="card accordion-item active">
                <h2 class="accordion-header" id="headingThree">
                  <button
                    type="button"
                    class="accordion-button"
                    data-bs-toggle="collapse"
                    data-bs-target="#accordionThree"
                    aria-expanded="true"
                    aria-controls="accordionThree">
                    Does this platform support multiple brokers?
                  </button>
                </h2>

                <div
                  id="accordionThree"
                  class="accordion-collapse collapse show"
                  data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    Yes. You can connect Demat accounts from <strong>multiple supported brokers</strong> and view
                    all holdings, valuations, and performance in a single unified dashboard.
                    New broker integrations are added regularly.
                  </div>
                </div>
              </div>

              <!-- FAQ 4 -->
              <div class="card accordion-item">
                <h2 class="accordion-header" id="headingFour">
                  <button
                    type="button"
                    class="accordion-button collapsed"
                    data-bs-toggle="collapse"
                    data-bs-target="#accordionFour"
                    aria-expanded="false"
                    aria-controls="accordionFour">
                    Can I place trades through this platform?
                  </button>
                </h2>

                <div id="accordionFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    No. This platform is designed for <strong>portfolio tracking and analysis only</strong>.
                    All trading actions continue to happen directly on your broker’s platform.
                    This ensures compliance, security, and zero execution risk.
                  </div>
                </div>
              </div>

              <!-- FAQ 5 -->
              <div class="card accordion-item">
                <h2 class="accordion-header" id="headingFive">
                  <button
                    type="button"
                    class="accordion-button collapsed"
                    data-bs-toggle="collapse"
                    data-bs-target="#accordionFive"
                    aria-expanded="false"
                    aria-controls="accordionFive">
                    Is this suitable for investment advisors?
                  </button>
                </h2>

                <div id="accordionFive" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    Absolutely. The <strong>Advisor plan</strong> is built for wealth managers and advisors
                    to monitor multiple client portfolios, generate reports, and track performance —
                    all while maintaining strict read-only access.
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- FAQ: End -->

    <!-- CTA: Start -->
    <section id="landingCTA" class="section-py landing-cta position-relative p-lg-0 pb-0">
      <img
        src="{{ asset('assets/img/front-pages/backgrounds/cta-bg-' . $configData['theme'] . '.png') }}"
        class="position-absolute bottom-0 end-0 scaleX-n1-rtl h-100 w-100 z-n1"
        alt="cta background"
        data-app-light-img="front-pages/backgrounds/cta-bg-light.png"
        data-app-dark-img="front-pages/backgrounds/cta-bg-dark.png"
      />

      <div class="container">
        <div class="row align-items-center gy-12">

          <!-- Text -->
          <div class="col-lg-6 text-start text-sm-center text-lg-start">
            <h3 class="cta-title text-primary fw-bold mb-1">
              Manage All Your Demat Accounts in One Place
            </h3>
            <h5 class="text-body mb-8">
              Track holdings, performance, and insights across brokers — securely and effortlessly.
            </h5>
            <a href="{{ url('/front-pages/payment') }}" class="btn btn-lg btn-primary">
              Start Free Trial
            </a>
          </div>

          <!-- Image -->
          <div class="col-lg-6 pt-lg-12 text-center text-lg-end">
            <img
              src="{{ asset('assets/img/front-pages/landing-page/cta-dashboard.png') }}"
              alt="portfolio dashboard preview"
              class="img-fluid mt-lg-4"
            />
          </div>

        </div>
      </div>
    </section>
    <!-- CTA: End -->

    <!-- Contact Us: Start -->
    <section id="landingContact" class="section-py bg-body landing-contact">
      <div class="container">
        <div class="text-center mb-4">
          <span class="badge bg-label-primary">Contact Us</span>
        </div>

        <h4 class="text-center mb-1">
      <span class="position-relative fw-extrabold z-1">
        Get in Touch
        <img
          src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}"
          alt="section decoration"
          class="section-title-img position-absolute object-fit-contain bottom-0 z-n1"
        />
      </span>
          with Our Team
        </h4>

        <p class="text-center mb-12 pb-md-4">
          Have questions about plans, demat limits, or onboarding?
          We’re here to help.
        </p>

        <div class="row g-6">

          <!-- Contact Info -->
          <div class="col-lg-5">
            <div class="contact-img-box position-relative border p-2 h-100">
              <img
                src="{{ asset('assets/img/front-pages/icons/contact-border.png') }}"
                alt="contact border"
                class="contact-border-img position-absolute d-none d-lg-block scaleX-n1-rtl"
              />

              <img
                src="{{ asset('assets/img/front-pages/landing-page/contact-customer-service.png') }}"
                alt="customer support"
                class="contact-img w-100 scaleX-n1-rtl"
              />

              <div class="p-4 pb-2">
                <div class="row g-4">

                  <!-- Email -->
                  <div class="col-md-6 col-lg-12 col-xl-6">
                    <div class="d-flex align-items-center">
                      <div class="badge bg-label-primary rounded p-1_5 me-3">
                        <i class="icon-base ti tabler-mail icon-lg"></i>
                      </div>
                      <div>
                        <p class="mb-0">Email</p>
                        <h6 class="mb-0">
                          <a href="mailto:support@dmatx.com" class="text-heading">
                            support@dmatx.com
                          </a>
                        </h6>
                      </div>
                    </div>
                  </div>

                  <!-- Phone -->
                  <div class="col-md-6 col-lg-12 col-xl-6">
                    <div class="d-flex align-items-center">
                      <div class="badge bg-label-success rounded p-1_5 me-3">
                        <i class="icon-base ti tabler-phone-call icon-lg"></i>
                      </div>
                      <div>
                        <p class="mb-0">Phone</p>
                        <h6 class="mb-0">
                          <a href="tel:+91XXXXXXXXXX" class="text-heading">
                            +91 XXXXX XXXXX
                          </a>
                        </h6>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="col-lg-7">
            <div class="card h-100">
              <div class="card-body">

                <h4 class="mb-2">Send Us a Message</h4>
                <p class="mb-6">
                  Reach out for support related to account limits, pricing plans,
                  advisor onboarding, or general platform queries.
                </p>

                <form>
                  <div class="row g-4">

                    <div class="col-md-6">
                      <label class="form-label" for="contact-form-fullname">
                        Full Name
                      </label>
                      <input
                        type="text"
                        class="form-control"
                        id="contact-form-fullname"
                        placeholder="Enter your name"
                      />
                    </div>

                    <div class="col-md-6">
                      <label class="form-label" for="contact-form-email">
                        Email Address
                      </label>
                      <input
                        type="email"
                        id="contact-form-email"
                        class="form-control"
                        placeholder="you@example.com"
                      />
                    </div>

                    <div class="col-12">
                      <label class="form-label" for="contact-form-message">
                        Message
                      </label>
                      <textarea
                        id="contact-form-message"
                        class="form-control"
                        rows="7"
                        placeholder="Tell us how we can help you"
                      ></textarea>
                    </div>

                    <div class="col-12">
                      <button type="submit" class="btn btn-primary">
                        Send Message
                      </button>
                    </div>

                  </div>
                </form>

              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
    <!-- Contact Us: End -->
  </div>
@endsection
