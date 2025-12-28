@php
    use Illuminate\Support\Facades\Route;
    $configData = Helper::appClasses();
@endphp
<!-- Horizontal Menu -->
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal  menu bg-menu-theme flex-grow-0"
    @foreach ($configData['menuAttributes'] as $attribute => $value)
  {{ $attribute }}="{{ $value }}" @endforeach>
    <div class="{{ $containerNav }} d-flex h-100">
        <ul class="menu-inner">
            @foreach ($menuData[1]->menu as $menu)
                {{-- active menu method --}}
                @php
                  $activeClass = null;
                  $currentRouteName = Route::currentRouteName();

                  // Check if slug is an array
                  if (is_array($menu->slug)) {
                      if (in_array($currentRouteName, $menu->slug)) {
                          $activeClass = 'active';
                      } else {
                          // Fallback for partial matches (e.g., accounts.create)
                          foreach ($menu->slug as $slug) {
                              if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                                  $activeClass = 'active';
                                  break;
                              }
                          }
                      }
                  } else {
                      // Handle string slug
                      if ($currentRouteName === $menu->slug || (str_contains($currentRouteName, $menu->slug) && strpos($currentRouteName, $menu->slug) === 0)) {
                          $activeClass = 'active';
                      }
                  }
                @endphp

                {{-- main menu --}}
                <li class="menu-item {{ $activeClass }}">
                    <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                        class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
                        @isset($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endisset
                        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                    </a>

                    {{-- submenu --}}
                    @isset($menu->submenu)
                        @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
                    @endisset
                </li>
            @endforeach
        </ul>
    </div>
</aside>
<!--/ Horizontal Menu -->
