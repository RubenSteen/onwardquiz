<template>
    <layout title="Users">
        <!-- Replace with your content -->
        <div class="py-4">
            <!-- Begin card header -->
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap">
                    <div class="ml-4 mt-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Users</h3>
                    </div>
                </div>
            </div>
            <!-- /End card header -->

            <div v-if="users.length === 0" class="h-64 flex justify-center">
                <h3 class="text-lg leading-6 font-medium text-gray-400 py-4 self-center">Nothing here...</h3>
            </div>

            <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <li
                    v-for="(user, index) in users"
                    :key="user.id"
                    class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow"
                >
                    <div class="flex-1 flex flex-col p-8">
                        <img
                            v-if="user.avatar"
                            class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full"
                            :src="user.avatar"
                            :alt="'Discord avatar of ' + user.getFullUsername"
                        />
                        <span
                            v-else
                            class="w-32 h-32 flex-shrink-0 mx-auto inline-block rounded-full overflow-hidden bg-gray-100"
                        >
                            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                        </span>
                        <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">{{ user.getFullUsername }}</h3>
                        <dl class="mt-1 flex-grow flex flex-col justify-between">
                            <dt class="sr-only">Email</dt>
                            <dd class="text-gray-500 text-sm leading-5">{{ user.email }}</dd>
                            <dd class="text-gray-500 text-sm leading-5">
                                Last activity : <i>{{ user.last_activity }}</i>
                            </dd>
                            <dt class="sr-only">Roles</dt>
                            <dd class="mt-3">
                                <span
                                    v-show="user.isSuperAdmin"
                                    class="px-2 py-1 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full"
                                    >Super Admin</span
                                >
                                <span
                                    v-show="user.isEditor"
                                    class="px-2 py-1 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full"
                                    >Editor</span
                                >
                                <span
                                    v-show="user.isEditor == false && user.isSuperAdmin == false"
                                    class="px-2 py-1 text-gray-800 text-xs leading-4 font-medium bg-gray-100 rounded-full"
                                    >No roles</span
                                >
                            </dd>
                            <dt class="sr-only">Status</dt>
                            <dd class="mt-3">
                                <span
                                    v-show="user.isConfirmed"
                                    class="px-2 py-1 text-blue-800 text-xs leading-4 font-medium bg-blue-100 rounded-full"
                                    >Confirmed</span
                                >
                                <span
                                    v-show="user.isBanned"
                                    class="px-2 py-1 text-red-800 text-xs leading-4 font-medium bg-red-100 rounded-full"
                                    >Banned</span
                                >
                                <span
                                    v-show="user.isConfirmed == false && user.isBanned == false"
                                    class="px-2 py-1 text-gray-800 text-xs leading-4 font-medium bg-gray-100 rounded-full"
                                    >No statuses</span
                                >
                            </dd>
                        </dl>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="-mt-px flex">
                            <div class="w-0 flex-1 flex border-r border-gray-200">
                                <div
                                    @click="selectedUser = user"
                                    class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150"
                                >
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                        <path
                                            fill-rule="evenodd"
                                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                    <span class="ml-3">Quick actions</span>
                                </div>
                            </div>
                            <!-- <div class="-ml-px w-0 flex-1 flex">
                  <inertia-link href="#" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">View profile</span>
                  </inertia-link>
                </div> -->
                        </div>
                    </div>
                </li>
            </ul>

            <div
                v-show="selectedUser != null"
                class="fixed bottom-0 inset-x-0 px-4 pb-6 sm:inset-0 sm:p-0 sm:flex sm:items-center sm:justify-center"
            >
                <!--
          Background overlay, show/hide based on modal state.

          Entering: "ease-out duration-300"
            From: "opacity-0"
            To: "opacity-100"
          Leaving: "ease-in duration-200"
            From: "opacity-100"
            To: "opacity-0"
        -->
                <div
                    v-show="selectedUser != null"
                    @click="selectedUser = null"
                    class="fixed inset-0 transition-opacity"
                >
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <!--
          Modal panel, show/hide based on modal state.

          Entering: "ease-out duration-300"
            From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            To: "opacity-100 translate-y-0 sm:scale-100"
          Leaving: "ease-in duration-200"
            From: "opacity-100 translate-y-0 sm:scale-100"
            To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        -->
                <div
                    v-if="selectedUser != null"
                    class="bg-white rounded-lg px-4 pt-5 pb-4 overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full sm:p-6"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="modal-headline"
                >
                    <div>
                        <div class="mx-auto flex items-center justify-center">
                            <img
                                v-if="selectedUser.avatar"
                                class="h-12 w-12 flex-shrink-0 mx-auto bg-black rounded-full"
                                :src="selectedUser.avatar"
                                :alt="'Discord avatar of ' + selectedUser.getFullUsername"
                            />
                            <span
                                v-else
                                class="h-12 w-12 flex-shrink-0 mx-auto inline-block rounded-full overflow-hidden bg-gray-100"
                            >
                                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                            </span>
                            <!-- <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg> -->
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                {{ selectedUser.getFullUsername }}
                            </h3>
                            <div class="mt-2">
                                <form>
                                    <div>
                                        <div class="">
                                            <div class="mt-6 sm:mt-5">
                                                <div class="sm:border-t sm:border-gray-200 sm:pt-5">
                                                    <div role="group" aria-labelledby="label-email">
                                                        <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                                                            <div>
                                                                <div
                                                                    class="text-base leading-6 font-medium text-gray-900 sm:text-sm sm:leading-5 sm:text-gray-700"
                                                                    id="label-email"
                                                                >
                                                                    Actions
                                                                </div>
                                                            </div>
                                                            <div class="mt-4 sm:mt-0 sm:col-span-2">
                                                                <div class="max-w-lg">
                                                                    <div class="relative flex items-start">
                                                                        <div class="flex items-center h-5">
                                                                            <input
                                                                                v-model="form.banned"
                                                                                id="banned"
                                                                                type="checkbox"
                                                                                class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                                            />
                                                                        </div>
                                                                        <div class="ml-3 text-sm leading-5">
                                                                            <label
                                                                                for="banned"
                                                                                class="font-medium text-gray-700"
                                                                                >Banned</label
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-4">
                                                                        <div class="relative flex items-start">
                                                                            <div class="flex items-center h-5">
                                                                                <input
                                                                                    v-model="form.confirmed"
                                                                                    id="confirmed"
                                                                                    type="checkbox"
                                                                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                                                />
                                                                            </div>
                                                                            <div class="ml-3 text-sm leading-5">
                                                                                <label
                                                                                    for="confirmed"
                                                                                    class="font-medium text-gray-700"
                                                                                    >Confirmed</label
                                                                                >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-4">
                                                                        <div class="relative flex items-start">
                                                                            <div class="flex items-center h-5">
                                                                                <input
                                                                                    v-model="form.editor"
                                                                                    id="editor"
                                                                                    type="checkbox"
                                                                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                                                />
                                                                            </div>
                                                                            <div class="ml-3 text-sm leading-5">
                                                                                <label
                                                                                    for="editor"
                                                                                    class="font-medium text-gray-700"
                                                                                    >Editor</label
                                                                                >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-4">
                                                                        <div class="relative flex items-start">
                                                                            <div class="flex items-center h-5">
                                                                                <input
                                                                                    v-model="form.super_admin"
                                                                                    id="super-admin"
                                                                                    type="checkbox"
                                                                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                                                />
                                                                            </div>
                                                                            <div class="ml-3 text-sm leading-5">
                                                                                <label
                                                                                    for="super-admin"
                                                                                    class="font-medium text-gray-700"
                                                                                    >Super Admin</label
                                                                                >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <span class="flex w-full rounded-md shadow-sm sm:col-start-2">
                            <button
                                @click="updateUser"
                                type="button"
                                class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-indigo-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                            >
                                Submit
                            </button>
                        </span>
                        <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:col-start-1">
                            <button
                                @click="selectedUser = null"
                                type="button"
                                class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                            >
                                Cancel
                            </button>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Begin pagination -->
            <pagination class="mt-4" :links="usersPagination" />
            <!-- /End pagination -->
        </div>
        <!-- /End replace -->
    </layout>
</template>

<script>
import Layout from '../../Shared/Layout';
import Pagination from '../../Shared/Pagination';

export default {
    components: {
        Layout,
        Pagination,
    },
    props: {
        users: Array,
        usersPagination: Array,
    },
    data() {
        return {
            selectedUser: null,
            form: {
                banned: false,
                confirmed: false,
                editor: false,
                super_admin: false,
            },
            loading: false,
        };
    }, // End Data
    methods: {
        updateUser() {
            this.loading = true;

            var data = new FormData();

            data.append('_method', 'PATCH');

            for (var field in this.form) {
                data.append(field, this.form[field]); // append form field to request
            }

            this.$inertia.post(route('admin.user.update', this.selectedUser.id), data).then(() => {
                this.loading = false;

                // Check if any errors exist
                if (Object.keys(this.$page.errors).length === 0) {
                    this.selectedUser = null;
                }
            });
        }, // End updateUser()
    }, // End Methods
    watch: {
        selectedUser: function () {
            if (this.selectedUser != null) {
                this.form.banned = this.selectedUser.isBanned;
                this.form.confirmed = this.selectedUser.isConfirmed;
                this.form.super_admin = this.selectedUser.isSuperAdmin;
                this.form.editor = this.selectedUser.isEditor;
            }
        }, // End selectedAnswer
    }, // End watch
};
</script>
