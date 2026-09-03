<nav x-data="{ open: false }" class="bg-primary-900">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-white" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.teachers')" :active="request()->routeIs('admin.teachers')">
                            {{ __('Data Guru') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.classes')" :active="request()->routeIs('admin.classes')">
                            {{ __('Data Kelas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.students')" :active="request()->routeIs('admin.students')">
                            {{ __('Data Siswa') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.parents')" :active="request()->routeIs('admin.parents')">
                            {{ __('Data Orang Tua') }}
                        </x-nav-link>
                        <x-nav-link :href="route('attendance.scan')" :active="request()->routeIs('attendance.scan')">
                            {{ __('Scan Presensi') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.permissions.index')" :active="request()->routeIs('teacher.permissions.*')">
                            {{ __('Persetujuan Izin') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                            {{ __('Laporan Presensi') }}
                        </x-nav-link>

                    @elseif(Auth::user()->role === 'teacher')
                        <x-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('attendance.scan')" :active="request()->routeIs('attendance.scan')">
                            {{ __('Scan Presensi') }}
                        </x-nav-link>
                        <x-nav-link :href="route('teacher.permissions.index')" :active="request()->routeIs('teacher.permissions.*')">
                            {{ __('Persetujuan Izin') }}
                        </x-nav-link>

                    @elseif(Auth::user()->role === 'student')
                        <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.scan')" :active="request()->routeIs('student.scan')">
                            {{ __('Scan QR Sekolah') }}
                        </x-nav-link>

                    @elseif(Auth::user()->role === 'parent')
                        <x-nav-link :href="route('parent.dashboard')" :active="request()->routeIs('parent.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('parent.permissions.index')" :active="request()->routeIs('parent.permissions.*')">
                            {{ __('Ajukan Izin') }}
                        </x-nav-link>

                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-100 bg-primary-800 hover:bg-primary-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="text-accent-600 hover:bg-accent-50">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-primary-200 hover:text-white hover:bg-primary-800 focus:outline-none focus:bg-primary-800 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-primary-900">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.teachers')" :active="request()->routeIs('admin.teachers')">
                    {{ __('Data Guru') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.classes')" :active="request()->routeIs('admin.classes')">
                    {{ __('Data Kelas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.students')" :active="request()->routeIs('admin.students')">
                    {{ __('Data Siswa') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.parents')" :active="request()->routeIs('admin.parents')">
                    {{ __('Data Orang Tua') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('attendance.scan')" :active="request()->routeIs('attendance.scan')">
                    {{ __('Scan Presensi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.permissions.index')" :active="request()->routeIs('teacher.permissions.*')">
                    {{ __('Persetujuan Izin') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                    {{ __('Laporan Presensi') }}
                </x-responsive-nav-link>

            @elseif(Auth::user()->role === 'teacher')
                <x-responsive-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('attendance.scan')" :active="request()->routeIs('attendance.scan')">
                    {{ __('Scan Presensi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('teacher.permissions.index')" :active="request()->routeIs('teacher.permissions.*')">
                    {{ __('Persetujuan Izin') }}
                </x-responsive-nav-link>

            @elseif(Auth::user()->role === 'student')
                <x-responsive-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.scan')" :active="request()->routeIs('student.scan')">
                    {{ __('Scan QR Sekolah') }}
                </x-responsive-nav-link>

            @elseif(Auth::user()->role === 'parent')
                <x-responsive-nav-link :href="route('parent.dashboard')" :active="request()->routeIs('parent.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('parent.permissions.index')" :active="request()->routeIs('parent.permissions.*')">
                    {{ __('Ajukan Izin') }}
                </x-responsive-nav-link>

            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-primary-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-primary-200">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>