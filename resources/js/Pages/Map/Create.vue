<template>
    <layout title="Create Map">
        <div class="font-bold text-xl mb-8">Create Map</div>

        <div class="w-full">
            <div>
                <div>
                    <div>
                        <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                            This information will be displayed publicly so be careful what you write down...
                        </p>
                    </div>
                    <div class="mt-6 sm:mt-5">
                        <div
                            class="
                                sm:grid sm:grid-cols-3
                                sm:gap-4
                                sm:items-start
                                sm:border-t sm:border-gray-200
                                sm:pt-5
                            "
                        >
                            <label
                                for="name"
                                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                            >
                                Name
                            </label>
                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                <div class="max-w-lg flex rounded-md shadow-sm">
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        v-on:keydown.enter="$refs.submit.click()"
                                        :class="{
                                            'border-red-500 text-red-900 placeholder-red-300 focus:border-red-500 focus:shadow-outline-red':
                                                $page.errors.name,
                                        }"
                                        class="
                                            flex-1
                                            form-input
                                            block
                                            w-full
                                            rounded-none rounded-md
                                            transition
                                            duration-150
                                            ease-in-out
                                            sm:text-sm
                                            sm:leading-5
                                        "
                                    />
                                </div>
                                <p
                                    class="mt-2 text-xs text-red-600"
                                    v-show="$page.errors.name"
                                    v-for="error in $page.errors.name"
                                >
                                    {{ error }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-200 pt-5">
                <div class="flex justify-end">
                    <span class="inline-flex rounded-md shadow-sm">
                        <inertia-link
                            :href="$route('map.index')"
                            class="
                                py-2
                                px-4
                                border border-gray-300
                                rounded-md
                                text-sm
                                leading-5
                                font-medium
                                text-gray-700
                                hover:text-gray-500
                                focus:outline-none
                                focus:border-blue-300
                                focus:shadow-outline-blue
                                active:bg-gray-50
                                active:text-gray-800
                                transition
                                duration-150
                                ease-in-out
                            "
                        >
                            Cancel
                        </inertia-link>
                    </span>
                    <span class="ml-3 inline-flex rounded-md shadow-sm">
                        <button
                            :disabled="loading"
                            @click="createMap"
                            type="button"
                            ref="submit"
                            :class="{
                                'cursor-not-allowed bg-indigo-400 text-gray-700': loading === true,
                                'text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700':
                                    loading === false,
                            }"
                            class="
                                inline-flex
                                justify-center
                                py-2
                                px-4
                                border border-transparent
                                text-sm
                                leading-5
                                font-medium
                                rounded-md
                                transition
                                duration-150
                                ease-in-out
                            "
                        >
                            Save
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from '../Shared/Layout';

export default {
    components: {
        Layout,
    }, // End Components
    remember: 'form',
    data() {
        return {
            form: {
                name: '',
            },
            loading: false,
        };
    }, // End Data
    methods: {
        createMap() {
            this.loading = true;

            var data = new FormData();

            data.append('_method', 'POST');

            for (var field in this.form) {
                data.append(field, this.form[field]); // append form field to request
            }

            this.$inertia.post(route('map.store'), data).then(() => {
                this.loading = false;
            });
        }, // End createMap()
    }, // End Methods
};
</script>
