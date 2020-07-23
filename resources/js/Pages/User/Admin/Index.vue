<template>
  <layout title="Users">
  
    <!-- Replace with your content -->
    <div class="py-4">

      <!-- Begin list -->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">

        <!-- Begin card header -->
        <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
          <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap">
            <div class="ml-4 mt-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Users
              </h3>
            </div>
          </div>
        </div>
        <!-- /End card header -->

        <div v-if="users.length === 0" class="h-64 flex justify-center">
          <h3 class="text-lg leading-6 font-medium text-gray-400 py-4 self-center">
            Nothing here...
          </h3>
        </div>
        <ul v-else>
          <li v-for="user in users">
            <inertia-link :href="$route('admin.user.edit', {user_id: user.id})"
              class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
              <div class="flex items-center px-4 py-4 sm:px-6">
                <div class="min-w-0 flex-1 flex items-center">
                  <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                    <div>
                      <div class="text-sm leading-5 font-medium text-indigo-600 truncate">{{ user.username }}<span>#{{ user.discriminator }}</span></div>
                      <div class="mt-2 flex items-center text-sm leading-5 text-gray-500">
                        <span class="truncate">{{ user.email }}</span>
                      </div>
                    </div>
                    <div class="hidden md:block">
                      <div>

                        <div class="text-sm leading-5 text-gray-900">
                          Last activity : 
                          <span v-if="user.last_activity === null" class="text-gray-400 text-xs">Never</span>
                          <time v-else class="text-gray-500">{{ user.last_activity }}</time>
                        </div>

                        <div class="flex flex-wrap">
                          <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 mr-8">
                            <svg v-if="user.isEditor === false" class="flex-shrink-0 mr-1 h-5 w-5 text-red-500"
                              fill="currentColor" viewBox="0 0 24 24">
                              <path class="heroicon-ui"
                                d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z" />
                            </svg>
                            <svg v-else class="flex-shrink-0 mr-1 h-5 w-5 text-green-400" fill="currentColor"
                              viewBox="0 0 20 20">
                              <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                            </svg>
                            Editor
                          </div>

                          <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 mr-8">
                            <svg v-if="user.isConfirmed === false" class="flex-shrink-0 mr-1 h-5 w-5 text-red-500"
                              fill="currentColor" viewBox="0 0 24 24">
                              <path class="heroicon-ui"
                                d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z" />
                            </svg>
                            <svg v-else class="flex-shrink-0 mr-1 h-5 w-5 text-green-400" fill="currentColor"
                              viewBox="0 0 20 20">
                              <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                            </svg>
                            Confirmed
                          </div>

                          <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 mr-8">
                            <svg v-if="user.isBanned === false" class="flex-shrink-0 mr-1 h-5 w-5 text-red-500"
                              fill="currentColor" viewBox="0 0 24 24">
                              <path class="heroicon-ui"
                                d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z" />
                            </svg>
                            <svg v-else class="flex-shrink-0 mr-1 h-5 w-5 text-green-400" fill="currentColor"
                              viewBox="0 0 20 20">
                              <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                            </svg>
                            Banned
                          </div>

                          <div class="mt-2 flex items-center text-sm leading-5 text-gray-500 mr-8">
                            <svg v-if="user.isSuperAdmin === false" class="flex-shrink-0 mr-1 h-5 w-5 text-red-500"
                              fill="currentColor" viewBox="0 0 24 24">
                              <path class="heroicon-ui"
                                d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z" />
                            </svg>
                            <svg v-else class="flex-shrink-0 mr-1 h-5 w-5 text-green-400" fill="currentColor"
                              viewBox="0 0 20 20">
                              <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                            </svg>
                            Super admin
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </inertia-link>
          </li>
        </ul>
      </div>
      <!-- /End list -->
      <!-- Begin pagination -->
      <pagination :links="usersPagination" />
      <!-- /End pagination -->
    </div>
    <!-- /End replace -->


  </layout>
  
</template>

<script>

  import Layout from '../../Shared/Layout'
  import Pagination from '../../Shared/Pagination'

  export default {
    components: {
      Layout,
      Pagination,
    },
    props: {
      users: Array,
      usersPagination: Array,
    },
  }
</script>