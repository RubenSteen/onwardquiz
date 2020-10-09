<template>
    <layout title="Editing Question">
        <div class="font-bold text-xl mb-8">Creating Question for : {{ map.name }} (#{{ map.id }})</div>

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
                            class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                        >
                            <label
                                for="callout"
                                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                            >
                                Callout
                            </label>
                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                <div class="max-w-lg flex rounded-md shadow-sm">
                                    <input
                                        id="callout"
                                        v-model="form.callout"
                                        v-on:keydown.enter="$refs.submit.click()"
                                        :class="{
                                            'border-red-500 text-red-900 placeholder-red-300 focus:border-red-500 focus:shadow-outline-red':
                                                $page.errors.callout,
                                        }"
                                        class="flex-1 form-input block w-full rounded-none rounded-md transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                    />
                                </div>
                                <p
                                    class="mt-2 text-xs text-red-600"
                                    v-show="$page.errors.callout"
                                    v-for="error in $page.errors.callout"
                                >
                                    {{ error }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label for="photo" class="block text-sm leading-5 font-medium text-gray-700"> Template </label>
                    <div class="mt-2 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            <span class="rounded-md shadow-sm flex">
                                <div>
                                    <input @change="getFileFromInput" type="file" ref="file" style="display: none" />
                                    <button
                                        @click="$refs.file.click()"
                                        type="button"
                                        :class="{
                                            'border-red-500 text-red-700 placeholder-red-300 focus:border-red-500 focus:shadow-outline-red':
                                                $page.errors.template,
                                        }"
                                        class="py-2 px-3 border border-gray-300 rounded-md text-sm leading-4 font-medium text-gray-600 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                                    >
                                        Choose template
                                    </button>
                                </div>
                            </span>
                        </div>
                        <small class="text-red-700 text-xs sm:text-red-500">
                            There always have to be 1 template picture for a question. <br />
                            When the question is created you will be able to add more pictures. <br />
                            Make sure the template being used is the same as the map template.<br /><br />
                            Click
                            <a :href="map.template.location" :download="map.template.name" class="italic text-red-600">
                                here
                            </a>
                            for the template
                        </small>
                        <div class="mt-4">
                            <p
                                class="mt-2 text-xs text-red-600"
                                v-show="$page.errors.template"
                                v-for="error in $page.errors.template"
                            >
                                {{ error }}
                            </p>
                            <div
                                v-if="form.template"
                                :class="{
                                    'border-red-500 text-red-900 placeholder-red-300': $page.errors.template,
                                    'border-indigo-500': !$page.errors.template,
                                }"
                                class="relative text-gray-500 text-xs border rounded-md w-auto box-border p-4"
                            >
                                <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                                    <button
                                        @click="form.template = null"
                                        type="button"
                                        class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150"
                                    >
                                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                                File to upload : {{ form.template.name }}<br />
                                Type : {{ form.template.type }}<br />
                                Size : {{ readableBytes(form.template.size) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="">
          <div class="mt-6 sm:mt-5">
            <div class="sm:border-t sm:border-gray-200 sm:pt-5">
              <fieldset>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">

                  <div>
                    <legend
                      class="text-base leading-6 font-medium text-gray-900 sm:text-sm sm:leading-5 sm:text-gray-700">
                      Template
                    </legend>
                    <small class="text-red-700 text-xs sm:text-red-500">
                      There always have to be 1 template picture for a question. <br>
                      When the question is created you will be able to add more picutes. <br>
                      For now make sure to use the parent (overview) template as a first picture.<br><br>
                      <a :href="map.template.location" target="_blank" type="button"
                        class="text-red-700 text-xs sm:text-red-600 hover:text-red-800 text-xs hover:sm:text-red-700 underline">
                        Click here for the template
                      </a>
                    </small>
                  </div>

                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    
                  </div>

                </div>
              </fieldset>
            </div>
          </div>
        </div> -->
            </div>
            <div class="mt-8 border-t border-gray-200 pt-5">
                <div class="flex justify-end">
                    <span class="inline-flex rounded-md shadow-sm">
                        <inertia-link
                            :href="$route('map.edit', { map_id: map.id })"
                            class="py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                        >
                            Cancel
                        </inertia-link>
                    </span>
                    <span class="ml-3 inline-flex rounded-md shadow-sm">
                        <button
                            :disabled="loading"
                            @click="createQuestion"
                            type="button"
                            ref="submit"
                            :class="{
                                'cursor-not-allowed bg-indigo-400 text-gray-700': loading === true,
                                'text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700':
                                    loading === false,
                            }"
                            class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md transition duration-150 ease-in-out"
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
import Layout from '@/Pages/Shared/Layout';

export default {
    components: {
        Layout,
    }, // End Components
    props: {
        map: Object,
    }, // End Props
    remember: 'form',
    data() {
        return {
            form: {
                callout: '',
                published: '',
                template: null,
            },
            loading: false,
        };
    }, // End Data
    methods: {
        createQuestion() {
            this.loading = true;

            var data = new FormData();

            data.append('_method', 'POST');

            for (var field in this.form) {
                data.append(field, this.form[field]); // append form field to request
            }

            this.$inertia.post(route('question.store', { map: this.map.id }), data).then(() => {
                this.loading = false;
            });
        }, // End createQuestion()
        getFileFromInput(event) {
            if (typeof event.target.files[0] !== 'undefined') {
                this.form.template = event.target.files[0];
            }
        }, //End getFileFromInput()
        readableBytes(bytes) {
            var i = Math.floor(Math.log(bytes) / Math.log(1024)),
                sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return (bytes / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
        }, // End readableBytes()
    }, // End Methods
};
</script>
