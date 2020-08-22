<template>
    <layout title="Editing Question">
        <div class="font-bold text-xl mb-8">Editing Question : {{ question.callout }} (#{{ question.id }})</div>

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
                                    v-for="(error, index) in $page.errors.callout"
                                    :key="index"
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
                    <label for="photo" class="block text-sm leading-5 font-medium text-gray-700">
                        Template
                    </label>
                    <div class="mt-2 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            <span class="h-48 w-64 md:w-64 md:h-64 overflow-hidden bg-gray-100 flex">
                                <div class="col-span-4 flex justify-center self-center">
                                    <img
                                        v-if="question.template"
                                        class="rounded-lg"
                                        :src="question.template.location"
                                        :alt="question.template.name"
                                    />
                                    <img
                                        v-else
                                        src="https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg"
                                        alt="No image"
                                    />
                                </div>
                            </span>
                            <span class="ml-5 rounded-md shadow-sm flex">
                                <div>
                                    <a
                                        :href="question.template.location"
                                        :download="question.template.name"
                                        class="py-2 px-3 border-2 border-indigo-600 hover:border-indigo-700 rounded-md text-sm leading-4 font-medium text-indigo-600 hover:text-indigo-700 focus:outline-none focus:border-indigo-800 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                                    >
                                        Download
                                    </a>
                                    <input @change="getFileFromInput" type="file" ref="file" style="display: none;" />
                                    <button
                                        @click="$refs.file.click()"
                                        type="button"
                                        :class="{
                                            'border-red-500 text-red-700 placeholder-red-300 focus:border-red-500 focus:shadow-outline-red':
                                                $page.errors.template,
                                        }"
                                        class="ml-2 py-2 px-3 border border-gray-300 rounded-md text-sm leading-4 font-medium text-gray-600 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                                    >
                                        Change
                                    </button>
                                </div>
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-red-500">
                            Make sure the template being used is the same as the map image <br />
                            Click
                            <a
                                :href="question.map.template.location"
                                :download="question.map.template.name"
                                class="italic text-red-600"
                            >
                                here
                            </a>
                            for the image
                        </p>
                        <div class="mt-4">
                            <p
                                class="mt-2 text-xs text-red-600"
                                v-show="$page.errors.template"
                                v-for="(error, index) in $page.errors.template"
                                :key="index"
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

                <div class="">
                    <div class="mt-6 sm:mt-5">
                        <div class="sm:border-t sm:border-gray-200 sm:pt-5">
                            <fieldset>
                                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                                    <div>
                                        <legend
                                            class="text-base leading-6 font-medium text-gray-900 sm:text-sm sm:leading-5 sm:text-gray-700"
                                        >
                                            Pictures
                                        </legend>
                                        <small class="text-gray-700 text-xs sm:text-gray-500">
                                            Be creative! add some more pictures of the callout. perhaps ingame pictures
                                            from the sky or player perspective and rate them on the difficulty!
                                            <br /><br />
                                            Click on one of the images to edit a picture
                                        </small>

                                        <div class="flex justify-center mt-4">
                                            <span class="inline-flex rounded-md shadow-sm">
                                                <button
                                                    :disabled="loading"
                                                    @click="setPictureModal(true)"
                                                    type="button"
                                                    :class="{
                                                        'cursor-not-allowed bg-indigo-400 text-gray-700':
                                                            loading === true,
                                                        'text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700':
                                                            loading === false,
                                                    }"
                                                    class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md transition duration-150 ease-in-out"
                                                >
                                                    Add a picture
                                                </button>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Begin question picture -->
                                    <question-pictures
                                        :show-modal="pictureModal"
                                        v-on:showPictureModal="setPictureModal"
                                        :pictures="question.pictures"
                                        :map-id="question.map.id"
                                        :question-id="question.id"
                                        v-on:loading="setLoading"
                                    ></question-pictures>
                                    <!-- /End question picture -->

                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="mt-6 sm:mt-5 mb-5">
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                        <label for="callout" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                            Similar Questions
                        </label>

                        <similar-questions
                            :map-id="question.map.id"
                            :question-id="question.id"
                            :similar-questions-ids="question.similar_question_ids"
                            :questions="question.map.questions"
                            :loading="loading"
                            v-on:loading="setLoading"
                        ></similar-questions>
                    </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="first_name" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                        Fake Answers
                        <small class="text-gray-700 text-xs sm:text-gray-500 block">
                            Be creative! add some fake callouts that might confuse them into guessing it! Try not to use
                            names that already exist in the questions itself
                        </small>
                    </label>

                    <fake-answers
                        :map-id="question.map.id"
                        :question-id="question.id"
                        :fake-answers="question.fakeAnswers"
                        :loading="loading"
                        v-on:loading="setLoading"
                    ></fake-answers>
                </div>

                <div class="">
                    <div class="mt-6 sm:mt-5">
                        <div class="sm:border-t sm:border-gray-200 sm:pt-5">
                            <fieldset>
                                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                                    <div>
                                        <legend
                                            class="text-base leading-6 font-medium text-gray-900 sm:text-sm sm:leading-5 sm:text-gray-700"
                                        >
                                            Others
                                        </legend>
                                    </div>
                                    <div class="mt-4 sm:mt-0 sm:col-span-2">
                                        <div class="max-w-lg">
                                            <div class="relative flex items-start">
                                                <div class="absolute flex items-center h-5">
                                                    <input
                                                        v-model="form.published"
                                                        id="published"
                                                        type="checkbox"
                                                        class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                    />
                                                </div>
                                                <div class="pl-7 text-sm leading-5">
                                                    <label for="comments" class="font-medium text-gray-700"
                                                        >Published</label
                                                    >
                                                    <p
                                                        class="mt-2 text-xs text-red-600"
                                                        v-show="$page.errors.published"
                                                        v-for="(error, index) in $page.errors.published"
                                                        :key="index"
                                                    >
                                                        {{ error }}
                                                    </p>
                                                    <p class="text-gray-500">
                                                        Make the question available to the public
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-200 pt-5">
                <div class="flex justify-end">
                    <span class="inline-flex rounded-md shadow-sm">
                        <inertia-link
                            :href="$route('map.edit', { map_id: question.map.id })"
                            class="py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                        >
                            Cancel
                        </inertia-link>
                    </span>
                    <span class="ml-3 inline-flex rounded-md shadow-sm">
                        <button
                            :disabled="loading"
                            @click="updateQuestion"
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
import SimilarQuestions from '@/Pages/Question/Components/Similar-Questions';
import FakeAnswers from '@/Pages/Question/Components/Fake-Answers';
import QuestionPictures from '@/Pages/Question/Components/Question-Pictures';
import Layout from '@/Pages/Shared/Layout';

export default {
    components: {
        Layout,
        SimilarQuestions,
        FakeAnswers,
        QuestionPictures,
    }, // End Components
    props: {
        question: Object,
    }, // End Props
    data() {
        return {
            form: {
                callout: this.question.callout,
                published: this.question.published,
                template: null,
            },
            loading: false,
            pictureModal: false,
        };
    }, // End Data
    methods: {
        updateQuestion() {
            this.setLoading(true);

            var data = new FormData();

            data.append('_method', 'PATCH');

            for (var field in this.form) {
                if (field === 'template' && this.form[field] === null) {
                    // If the template field is null then do not send.
                    continue;
                } else {
                    data.append(field, this.form[field]); // append form field to request
                }
            }

            this.$inertia
                .post(route('question.update', { map: this.question.map.id, question: this.question.id }), data)
                .then(() => {
                    this.setLoading(false);
                    this.form.template = null;
                });
        }, // End updateQuestion()

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

        setLoading(bool) {
            this.loading = bool;
        },

        setPictureModal(bool) {
            this.pictureModal = bool;
        },
    }, // End Methods
    watch: {
        'form.published'(newVal) {
            if (newVal === true) {
                this.form.published = 1;
            } else if (newVal === false) {
                this.form.published = 0;
            }
        },
    }, // End Watch
};
</script>
