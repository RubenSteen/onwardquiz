<template>
    <layout title="Maps" v-on:breadcrumbs="hideBreadcrumbs()">
        <!-- Replace with your content -->
        <div class="py-4">
            <!-- Begin list -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <!-- Begin card header -->
                <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                    <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap">
                        <div class="ml-4 mt-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Maps</h3>
                            <p class="mt-1 text-sm leading-5 text-gray-500">
                                All maps that can be used for making quiz
                            </p>
                        </div>
                        <div class="ml-4 mt-4 flex-shrink-0">
                            <span class="inline-flex rounded-md shadow-sm">
                                <inertia-link
                                    :href="$route('map.create')"
                                    class="
                                        relative
                                        inline-flex
                                        items-center
                                        px-4
                                        py-2
                                        border border-transparent
                                        text-sm
                                        leading-5
                                        font-medium
                                        rounded-md
                                        text-white
                                        bg-indigo-600
                                        hover:bg-indigo-500
                                        focus:outline-none focus:shadow-outline
                                    "
                                >
                                    Create new map
                                </inertia-link>
                            </span>
                        </div>
                    </div>
                </div>
                <!-- /End card header -->
                <div v-if="maps.length === 0" class="h-64 flex justify-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-400 py-4 self-center">Nothing here...</h3>
                </div>
                <ul v-else>
                    <li v-for="map in maps">
                        <inertia-link
                            :href="$route('map.edit', { map_id: map.id })"
                            class="
                                block
                                hover:bg-gray-50
                                focus:outline-none focus:bg-gray-50
                                transition
                                duration-150
                                ease-in-out
                            "
                        >
                            <div class="flex items-center px-2 py-2 sm:px-6">
                                <div class="min-w-0 flex-1 flex items-center">
                                    <div class="flex-shrink-0">
                                        <img
                                            v-if="map.template"
                                            class="h-16 w-16 rounded-md"
                                            :src="map.template.location"
                                            :alt="map.template.name"
                                        />
                                        <img
                                            v-else
                                            class="h-16 w-16 rounded-md"
                                            src="https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg"
                                            alt="No image available"
                                        />
                                    </div>
                                    <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                                        <div>
                                            <div class="text-sm leading-5 font-medium text-indigo-600 truncate">
                                                {{ map.name }}
                                            </div>
                                            <div class="mt-2 flex items-center text-sm leading-5 text-gray-500">
                                                <svg
                                                    class="flex-shrink-0 h-3 w-3 text-gray-400"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        class="heroicon-ui"
                                                        d="M11.03 8h3.94l1.06-4.24a1 1 0 1 1 1.94.48L17.03 8H20a1 1 0 0 1 0 2h-3.47l-1 4H18a1 1 0 1 1 0 2h-2.97l-1.06 4.25a1 1 0 1 1-1.94-.49l.94-3.76H9.03l-1.06 4.25a1 1 0 1 1-1.94-.49L6.97 16H4a1 1 0 0 1 0-2h3.47l1-4H6a1 1 0 0 1 0-2h2.97l1.06-4.24a1 1 0 1 1 1.94.48L11.03 8zm-.5 2l-1 4h3.94l1-4h-3.94z"
                                                    />
                                                </svg>
                                                <span class="truncate">{{ map.id }}</span>
                                            </div>
                                        </div>
                                        <div class="hidden md:block">
                                            <div>
                                                <!-- <div class="text-sm leading-5 text-gray-900">
                          Some text
                        </div> -->

                                                <div class="mt-2 flex items-center text-sm leading-5 text-gray-500">
                                                    <svg
                                                        v-if="map.published"
                                                        class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                    <svg
                                                        v-else
                                                        class="flex-shrink-0 mr-1.5 h-5 w-5 text-red-500"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            class="heroicon-ui"
                                                            d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"
                                                        />
                                                    </svg>
                                                    Published
                                                </div>

                                                <div class="mt-2 flex items-center text-sm leading-5 text-gray-500">
                                                    <svg
                                                        v-if="map.questionCount === 0"
                                                        class="flex-shrink-0 mr-1.5 h-5 w-5 text-red-500"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            class="heroicon-ui"
                                                            d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"
                                                        />
                                                    </svg>
                                                    <svg
                                                        v-else
                                                        class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                    {{ map.questionCount }} Question(s)
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </inertia-link>
                    </li>
                </ul>
            </div>
            <!-- /End list -->
            <!-- Begin pagination -->
            <pagination :links="mapsPagination" />
            <!-- /End pagination -->
        </div>
        <!-- /End replace -->
    </layout>
</template>

<script>
import Layout from '../Shared/Layout';
import Pagination from '../Shared/Pagination';

export default {
    components: {
        Layout,
        Pagination,
    },
    props: {
        maps: Array,
        mapsPagination: Array,
    },
    data() {
        return {
            sidebarOpen: false,
        };
    }, // End Data
};
</script>
