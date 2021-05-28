<template>
    <div>
        <!-- 
          Example

          return redirect()->back()->with('warning', [
            'title' => "Some Title",
            'message' => "Map was successfully updated!",
            'links' => [
                [
                    'name' => 'View status',
                    'target' => '#status',
                ],
                [
                    'name' => 'Undo',
                    'target' => '#undo',
                ],
            ],
        ]); 
        
        -->

        <!-- Success -->
        <div v-if="$page.flash.success" class="rounded-md bg-green-100 p-4 my-2">
            <div class="rounded-md bg-green-100 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div v-if="typeof $page.flash.success === 'object'" class="ml-3">
                        <h3 v-if="$page.flash.success.title" class="text-sm leading-5 font-medium text-green-800">
                            {{ $page.flash.success.title }}
                        </h3>
                        <div v-if="$page.flash.success.title" class="mt-2 text-sm leading-5 text-green-700">
                            <p>
                                {{ $page.flash.success.message }}
                            </p>
                        </div>
                        <div v-if="$page.flash.success.links" class="mt-4">
                            <div class="-mx-2 -my-1.5 flex">
                                <inertia-link
                                    v-for="(link, index) in $page.flash.success.links"
                                    :key="index"
                                    :href="link.target"
                                    class="
                                        px-2
                                        py-1.5
                                        rounded-md
                                        text-sm
                                        leading-5
                                        font-medium
                                        text-green-800
                                        hover:bg-green-200
                                        focus:outline-none
                                        focus:bg-green-200
                                        transition
                                        ease-in-out
                                        duration-150
                                    "
                                >
                                    {{ link.name }}
                                </inertia-link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="ml-3">
                        <div class="text-sm leading-5 text-green-700">
                            <p>
                                {{ $page.flash.success }}
                            </p>
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button
                                class="
                                    inline-flex
                                    rounded-md
                                    p-1.5
                                    text-green-500
                                    hover:bg-green-200
                                    focus:outline-none
                                    focus:bg-green-200
                                    transition
                                    ease-in-out
                                    duration-150
                                "
                                @click="$page.flash.success = null"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div v-if="$page.flash.info" class="rounded-md bg-blue-100 p-4 my-2">
            <div class="rounded-md bg-blue-100 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div v-if="typeof $page.flash.info === 'object'" class="ml-3">
                        <h3 v-if="$page.flash.info.title" class="text-sm leading-5 font-medium text-blue-800">
                            {{ $page.flash.info.title }}
                        </h3>
                        <div v-if="$page.flash.info.title" class="mt-2 text-sm leading-5 text-blue-700">
                            <p>
                                {{ $page.flash.info.message }}
                            </p>
                        </div>
                        <div v-if="$page.flash.info.links" class="mt-4">
                            <div class="-mx-2 -my-1.5 flex">
                                <inertia-link
                                    v-for="(link, index) in $page.flash.info.links"
                                    :key="index"
                                    :href="link.target"
                                    class="
                                        px-2
                                        py-1.5
                                        rounded-md
                                        text-sm
                                        leading-5
                                        font-medium
                                        text-blue-800
                                        hover:bg-blue-200
                                        focus:outline-none
                                        focus:bg-blue-200
                                        transition
                                        ease-in-out
                                        duration-150
                                    "
                                >
                                    {{ link.name }}
                                </inertia-link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="ml-3">
                        <div class="text-sm leading-5 text-blue-700">
                            <p>
                                {{ $page.flash.info }}
                            </p>
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button
                                class="
                                    inline-flex
                                    rounded-md
                                    p-1.5
                                    text-blue-500
                                    hover:bg-blue-200
                                    focus:outline-none
                                    focus:bg-blue-200
                                    transition
                                    ease-in-out
                                    duration-150
                                "
                                @click="$page.flash.info = null"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error -->
        <div v-if="$page.flash.error" class="rounded-md bg-red-100 p-4 my-2">
            <div class="rounded-md bg-red-100 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div v-if="typeof $page.flash.error === 'object'" class="ml-3">
                        <h3 v-if="$page.flash.error.title" class="text-sm leading-5 font-medium text-red-800">
                            {{ $page.flash.error.title }}
                        </h3>
                        <div v-if="$page.flash.error.title" class="mt-2 text-sm leading-5 text-red-700">
                            <p>
                                {{ $page.flash.error.message }}
                            </p>
                        </div>
                        <div v-if="$page.flash.error.links" class="mt-4">
                            <div class="-mx-2 -my-1.5 flex">
                                <inertia-link
                                    v-for="(link, index) in $page.flash.error.links"
                                    :key="index"
                                    :href="link.target"
                                    class="
                                        px-2
                                        py-1.5
                                        rounded-md
                                        text-sm
                                        leading-5
                                        font-medium
                                        text-red-800
                                        hover:bg-red-200
                                        focus:outline-none
                                        focus:bg-red-200
                                        transition
                                        ease-in-out
                                        duration-150
                                    "
                                >
                                    {{ link.name }}
                                </inertia-link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="ml-3">
                        <div class="text-sm leading-5 text-red-700">
                            <p>
                                {{ $page.flash.error }}
                            </p>
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button
                                class="
                                    inline-flex
                                    rounded-md
                                    p-1.5
                                    text-red-500
                                    hover:bg-red-200
                                    focus:outline-none
                                    focus:bg-red-200
                                    transition
                                    ease-in-out
                                    duration-150
                                "
                                @click="$page.flash.error = null"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning -->
        <div v-if="$page.flash.warning" class="rounded-md bg-yellow-100 p-4 my-2">
            <div class="rounded-md bg-yellow-100 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div v-if="typeof $page.flash.warning === 'object'" class="ml-3">
                        <h3 v-if="$page.flash.warning.title" class="text-sm leading-5 font-medium text-yellow-800">
                            {{ $page.flash.warning.title }}
                        </h3>
                        <div v-if="$page.flash.warning.title" class="mt-2 text-sm leading-5 text-yellow-700">
                            <p>
                                {{ $page.flash.warning.message }}
                            </p>
                        </div>
                        <div v-if="$page.flash.warning.links" class="mt-4">
                            <div class="-mx-2 -my-1.5 flex">
                                <inertia-link
                                    v-for="(link, index) in $page.flash.warning.links"
                                    :key="index"
                                    :href="link.target"
                                    class="
                                        px-2
                                        py-1.5
                                        rounded-md
                                        text-sm
                                        leading-5
                                        font-medium
                                        text-yellow-800
                                        hover:bg-yellow-200
                                        focus:outline-none
                                        focus:bg-yellow-200
                                        transition
                                        ease-in-out
                                        duration-150
                                    "
                                >
                                    {{ link.name }}
                                </inertia-link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="ml-3">
                        <div class="text-sm leading-5 text-yellow-700">
                            <p>
                                {{ $page.flash.warning }}
                            </p>
                        </div>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button
                                class="
                                    inline-flex
                                    rounded-md
                                    p-1.5
                                    text-yellow-500
                                    hover:bg-yellow-200
                                    focus:outline-none
                                    focus:bg-yellow-200
                                    transition
                                    ease-in-out
                                    duration-150
                                "
                                @click="$page.flash.warning = null"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    methods: {}, // End Methods

    watch: {
        '$page.flash.success': function () {
            const sleep = (milliseconds) => {
                return new Promise((resolve) => setTimeout(resolve, milliseconds));
            };
            sleep(3000).then(() => {
                this.$page.flash.success = null;
            });
        },

        '$page.flash.info': function () {
            const sleep = (milliseconds) => {
                return new Promise((resolve) => setTimeout(resolve, milliseconds));
            };
            sleep(3200).then(() => {
                this.$page.flash.info = null;
            });
        },

        '$page.flash.error': function () {
            const sleep = (milliseconds) => {
                return new Promise((resolve) => setTimeout(resolve, milliseconds));
            };
            sleep(3400).then(() => {
                this.$page.flash.error = null;
            });
        },

        '$page.flash.warning': function () {
            const sleep = (milliseconds) => {
                return new Promise((resolve) => setTimeout(resolve, milliseconds));
            };
            sleep(3600).then(() => {
                this.$page.flash.warning = null;
            });
        },
    }, // End Watch
};
</script>
