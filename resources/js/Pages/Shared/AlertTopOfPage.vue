<template>
    <div>
        <div
            v-if="$page.flash.success && show"
            class="bg-green-600 rounded text-white px-4 py-3 shadow-md mb-2"
            role="alert"
        >
            <div class="flex">
                <div class="">
                    <svg
                        class="fill-current h-4 w-4 text-white mr-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">{{ $page.flash.success }}</p>
                </div>
            </div>
        </div>
        <div
            v-if="$page.flash.info && show"
            class="bg-blue-600 rounded text-white px-4 py-3 shadow-md mb-2"
            role="alert"
        >
            <div class="flex">
                <div class="">
                    <svg
                        class="fill-current h-4 w-4 text-white mr-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">{{ $page.flash.info }}</p>
                </div>
            </div>
        </div>
        <div
            v-if="$page.flash.warning && show"
            class="bg-yellow-600 rounded text-white px-4 py-3 shadow-md mb-2"
            role="alert"
        >
            <div class="flex">
                <div class="">
                    <svg
                        class="fill-current h-4 w-4 text-white mr-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">{{ $page.flash.warning }}</p>
                </div>
            </div>
        </div>
        <div
            v-if="($page.flash.error || Object.keys($page.errors).length > 0) && show"
            class="bg-red-600 rounded text-white px-4 py-3 shadow-md"
            role="alert"
        >
            <div class="flex">
                <div class="">
                    <svg
                        class="fill-current h-4 w-4 text-white mr-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="font-bold" v-if="$page.flash.error">{{ $page.flash.error }}</p>
                    <p class="font-bold" v-else>There are/is {{ Object.keys($page.errors).length }} form errors.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            show: false,
        };
    }, // End data
    watch: {
        '$page.flash': {
            handler() {
                this.show = true;
                if (!Object.keys(this.$page.errors).length > 0) {
                    setTimeout(() => (this.show = false), 3000);
                }
            },
            deep: true,
        },
    }, // End watch
    methods: {}, // End methods
    mounted: function () {}, //End mounted
};
</script>
