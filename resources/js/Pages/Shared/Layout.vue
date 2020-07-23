<template>
<div class="h-screen flex overflow-hidden bg-gray-100" @keyup.esc="sidebarOpen = false">
    <!-- Off-canvas menu for mobile -->
    <div x-show="sidebarOpen" :class="{'md:hidden': quizMode == false}">
      <div @click="sidebarOpen = false" v-show="sidebarOpen" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-30 transition-opacity ease-linear duration-300">
        <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
      </div>
      <div class="fixed inset-0 flex z-40">
        <div v-show="sidebarOpen" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="flex-1 flex flex-col max-w-xs w-full bg-white transform ease-in-out duration-300 ">
          <div class="absolute top-0 right-0 -mr-14 p-1">
            <button v-show="sidebarOpen" @click="sidebarOpen = false" class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600">
              <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <inertia-link :href="$route('home')" class="flex-shrink-0 flex items-center px-4">
              <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-logo-on-white.svg" alt="Workflow" />
            </inertia-link>
            <nav class="mt-5 px-2">
              <!-- Make sure to edit for Mobile and Desktop -->
              <inertia-link :href="$route('quiz.index')" v-bind:class="{ 'text-gray-900 bg-gray-100 hover:bg-gray-100 focus:bg-gray-200': currentActiveLink('quiz.index') }" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition ease-in-out duration-150">
                <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path>
                </svg>
                Take the quiz
              </inertia-link>
              <inertia-link v-if="$page.auth.user.isSuperAdmin" :href="$route('admin.map.index')" v-bind:class="{ 'text-gray-900 bg-gray-100 hover:bg-gray-100 focus:bg-gray-200': currentActiveLink('admin.map.index') }" class="group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition ease-in-out duration-150">
                <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-600 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"/>
                </svg>
                Maps
              </inertia-link>
              <inertia-link v-if="$page.auth.user.isSuperAdmin" :href="$route('admin.user.index')" v-bind:class="{ 'text-gray-900 bg-gray-100 hover:bg-gray-100 focus:bg-gray-200': currentActiveLink('admin.user.index') }" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition ease-in-out duration-150">
                <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Users
              </inertia-link>
              <!-- <inertia-link href="#" class="group flex items-center px-2 py-2 text-base leading-6 font-medium text-gray-900 rounded-md bg-gray-100 focus:outline-none focus:bg-gray-200 transition ease-in-out duration-150">
                <svg class="mr-4 h-6 w-6 text-gray-500 group-hover:text-gray-500 group-focus:text-gray-600 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"/>
                </svg>
                Dashboard
              </inertia-link>
              <inertia-link :href="$route('admin.team.index')" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-100 transition ease-in-out duration-150">
                <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Team
              </inertia-link>
              <inertia-link href="#" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-100 transition ease-in-out duration-150">
                <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                Projects
              </inertia-link>
              <inertia-link href="#" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-100 transition ease-in-out duration-150">
                <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Calendar
              </inertia-link>
              <inertia-link href="#" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-100 transition ease-in-out duration-150">
                <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                Documents
              </inertia-link>
              <inertia-link href="#" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-100 transition ease-in-out duration-150">
                <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Reports
              </inertia-link> -->
            </nav>
          </div>
          <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
            <div href="#" class="flex-shrink-0 group block focus:outline-none w-full">
              <div class="flex items-center">
                <div>
                  <span v-if="$page.auth.user.avatar == null" class="inline-block h-10 w-10 rounded-full overflow-hidden bg-gray-100">
                    <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                  </span>
                  <img v-else class="inline-block h-10 w-10 rounded-full" :src="$page.auth.user.avatar" alt="Discord avatar" />
                </div>
                <div class="ml-3">
                  <p class="text-base leading-6 font-medium text-gray-700 group-hover:text-gray-900">
                    {{ $page.auth.user.username }}
                  </p>
                  <inertia-link :href="$route('user.profile')" class="text-sm leading-5 font-medium text-gray-500 group-hover:text-gray-700 group-focus:underline transition ease-in-out duration-150">
                    View profile
                  </inertia-link>
                </div>
                <div class="ml-auto">
                  <svg @click="showLogoutModal = true; sidebarOpen = false" class="h-6 w-6 text-red-600 cursor-pointer" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path d="M10 6H6C4.89543 6 4 6.89543 4 8V18C4 19.1046 4.89543 20 6 20H16C17.1046 20 18 19.1046 18 18V14M14 4H20M20 4V10M20 4L10 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="flex-shrink-0 w-14">
          <!-- Force sidebar to shrink to fit close icon -->
        </div>
      </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div v-if="quizMode == false" class="hidden md:flex md:flex-shrink-0">
      <div class="flex flex-col w-64 border-r border-gray-200 bg-white">
        <div class="h-0 flex-1 flex flex-col pt-4 pb-4 overflow-y-auto">
          <inertia-link :href="$route('home')" class="flex items-center flex-shrink-0 px-4">
            <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-logo-on-white.svg" alt="Workflow" />
          </inertia-link>
          <!-- Sidebar component, swap this element with another sidebar if you like -->
          <nav class="mt-5 flex-1 px-2 bg-white">
            <!-- Make sure to edit for Mobile and Desktop -->
            <inertia-link :href="$route('quiz.index')" v-bind:class="{ 'text-gray-900 bg-gray-100 hover:bg-gray-100 focus:bg-gray-200': currentActiveLink('quiz.index') }" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition ease-in-out duration-150">
              <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path>
              </svg>
              Take the quiz
            </inertia-link>
            <inertia-link v-if="$page.auth.user.isSuperAdmin" :href="$route('admin.map.index')" v-bind:class="{ 'text-gray-900 bg-gray-100 hover:bg-gray-100 focus:bg-gray-200': currentActiveLink('admin.map.index') }" class="group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition ease-in-out duration-150">
              <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-600 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"/>
              </svg>
              Maps
            </inertia-link>
            <inertia-link v-if="$page.auth.user.isSuperAdmin" :href="$route('admin.user.index')" v-bind:class="{ 'text-gray-900 bg-gray-100 hover:bg-gray-100 focus:bg-gray-200': currentActiveLink('admin.user.index') }" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition ease-in-out duration-150">
              <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              Users
            </inertia-link>
          </nav>
        </div>
        <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
          <div class="flex-shrink-0 group block focus:outline-none w-full">
            <div class="flex items-center">
              <div>
                <span v-if="$page.auth.user.avatar == null" class="inline-block h-9 w-9 rounded-full overflow-hidden bg-gray-100">
                  <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </span>
                <img v-else class="inline-block h-9 w-9 rounded-full" :src="$page.auth.user.avatar" alt="Discord avatar" />
              </div>
              <div class="ml-3">
                <p class="text-sm leading-5 font-medium text-gray-700 group-hover:text-gray-900">
                  {{ $page.auth.user.username }}
                </p>
                <inertia-link :href="$route('user.profile')" class="text-xs leading-4 font-medium text-gray-500 group-hover:text-gray-700 group-focus:underline transition ease-in-out duration-150">
                  View profile
                </inertia-link>
              </div>
               <div class="ml-auto">
                <svg @click="showLogoutModal = true" class="h-6 w-6 text-red-600 cursor-pointer" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path d="M10 6H6C4.89543 6 4 6.89543 4 8V18C4 19.1046 4.89543 20 6 20H16C17.1046 20 18 19.1046 18 18V14M14 4H20M20 4V10M20 4L10 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="flex flex-col w-0 flex-1 overflow-hidden" :class="{'z-40' : !sidebarOpen}">
      <div class="pl-1 sm:pl-3" :class="{'md:hidden pt-1 sm:pt-3': quizMode == false}">
        <button @click.stop="sidebarOpen = true" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:bg-gray-200 transition ease-in-out duration-150" :class="{'absolute z-10 bg-black': quizMode == true}">
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
      <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none" :class="{'pt-2 pb-6 md:py-6': quizMode == false}" tabindex="0" x-data x-init="$el.focus()">

        <div class="mx-auto" :class="{'px-4 sm:px-6 md:px-8': quizMode == false}">
          
          <!-- Begin breadcrumbs -->
          <breadcrumbs v-if="showBreadcrumbs"></breadcrumbs>
          <!-- /End breadcrumbs -->



          <!-- Begin logout modal -->
          <div v-show="showLogoutModal" class="fixed bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center z-50">
            <div @click="showLogoutModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
              <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div v-show="showLogoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative bg-white rounded-lg px-4 pt-5 pb-4 overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full sm:p-6">
              <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                <button @click="showLogoutModal = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150">
                  <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Logout
                  </h3>
                  <div class="mt-2">
                    <p class="text-sm leading-5 text-gray-500">
                      Are you sure you want to logout?
                    </p>
                  </div>
                </div>
              </div>
              <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                  <button @click="logout" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Logout
                  </button>
                </span>
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                  <button @click="showLogoutModal = false" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Go back
                  </button>
                </span>
              </div>
            </div>
          </div>
          <!-- /End logout modal -->

          <slot v-on:breadcrumbs="hideBreadcrumbs"/>
            
        </div>
      </main>

      <div class="absolute bottom-0 sm:right-6 sm:bottom-4 w-full sm:w-2/4 md:w-5/12 lg:w-4/12 xl:w-4/12">
        <alerts></alerts>
      </div>

    </div>
  </div>
</template>

<script>
  // import LeftSideNavbar from '../Shared/LeftSideNavbar'
  // import TopNavbar from '../Shared/TopNavbar'
  // import AlertTopOfPage from '../Shared/AlertTopOfPage'

  import Breadcrumbs from './Breadcrumbs'
  import Alerts from './Alerts'

  export default {
    components: {
      // LeftSideNavbar,
      // TopNavbar,
      // AlertTopOfPage,
      Breadcrumbs,
      Alerts,
    }, // End components
    props: {
      title: String,
      quizMode: {
        type: Boolean,
        default: false
      },
    }, // End props
    data() {
      return {
        sidebarOpen: false,
        showBreadcrumbs: true,
        showLogoutModal: false,
      }
    }, // End Data
    methods: {
      logout () {
        this.$inertia.post(route('logout.post'))
          .then(() => {
            this.showLogoutModal = false;
          })
      }, // End logout()
      currentActiveLink(string) {
        if (route().current() == string) {
          return true
        }
        return false
      } //End currentActiveLink
    }, // End Methods
    watch: {
      title: {
        immediate: true,
        handler(title) {
          document.title = title
        },
      }, // End title
    }, // End watch
  }
</script>