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
                                                    @click="pictureModal('open')"
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

                                    <!-- <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <span class="h-48 w-64 md:w-64 md:h-64 overflow-hidden bg-gray-100 flex" v-for="(picture, index) in question.pictures" :key="index">
                      <div class="col-span-4 flex justify-center self-center">
                        <img class="rounded-lg" :src="picture.image.location" :alt="picture.image.name">
                      </div>
                    </span>
                  </div> -->

                                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                                        <div class="w-full grid grid-cols-3 grid-flow-row gap-2">
                                            <div
                                                class="bg-white overflow-hidden overflow-hidden shadow rounded-lg"
                                                v-for="(picture, index) in question.pictures"
                                                :key="index"
                                            >
                                                <div class="px-2 py-2 h-64">
                                                    <div class="col-span-4 flex justify-center self-center h-64">
                                                        <img
                                                            class="rounded-lg w-auto"
                                                            :src="picture.image.location"
                                                            :alt="picture.image.name"
                                                            @click="pictureModal('open', picture)"
                                                        />
                                                    </div>
                                                </div>
                                                <div
                                                    class="mt-4 border-t border-gray-200 px-4 py-4 sm:px-6 text-xs text-gray-800 flex flex-col items-center"
                                                >
                                                    <span>Name : {{ picture.image.name }}</span>
                                                    <span>Size : {{ readableBytes(picture.image.size) }}</span>
                                                    <span>Type : {{ picture.image.type }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="w-full grid grid-cols-3 grid-flow-row gap-2">
                      <div class="bg-black h-64 overflow-hidden flex" v-for="(picture, index) in question.pictures" :key="index">
                        <div class="col-span-4 flex justify-center self-center">
                          <img class="rounded-lg" :src="picture.image.location" :alt="picture.image.name">
                        </div>
                      </div>
                    </div>
                  </div> -->

                                    <!-- Begin logout modal -->
                                    <div
                                        v-show="picture.modal"
                                        class="fixed bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center z-10"
                                    >
                                        <div
                                            @click="pictureModal('close')"
                                            x-transition:enter="ease-out duration-300"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            x-transition:leave="ease-in duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            class="fixed inset-0 transition-opacity"
                                        >
                                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                        </div>

                                        <div
                                            v-show="picture.modal"
                                            x-transition:enter="ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                            x-transition:leave="ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            class="relative bg-white rounded-lg px-4 pt-5 pb-4 overflow-hidden shadow-xl transform transition-all sm:max-w-6xl sm:w-full sm:p-6"
                                        >
                                            <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                                                <button
                                                    @click="pictureModal('close')"
                                                    type="button"
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150"
                                                >
                                                    <svg
                                                        class="h-6 w-6"
                                                        stroke="currentColor"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12"
                                                        />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="sm:flex sm:items-start">
                                                <div
                                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10"
                                                >
                                                    <svg
                                                        class="h-6 w-6 text-indigo-600"
                                                        stroke="currentColor"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            d="M4 16L8.58579 11.4142C9.36683 10.6332 10.6332 10.6332 11.4142 11.4142L16 16M14 14L15.5858 12.4142C16.3668 11.6332 17.6332 11.6332 18.4142 12.4142L20 14M14 8H14.01M6 20H18C19.1046 20 20 19.1046 20 18V6C20 4.89543 19.1046 4 18 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20Z"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                                        {{
                                                            picture.form.id !== null
                                                                ? 'Editing a Picture'
                                                                : 'Adding a Picture'
                                                        }}
                                                    </h3>
                                                    <div class="mt-2">
                                                        <div class="w-full">
                                                            <div>
                                                                <div>
                                                                    <div>
                                                                        <p
                                                                            class="mt-1 max-w-2xl text-sm leading-5 text-gray-500"
                                                                        >
                                                                            This information will be displayed publicly
                                                                            so be careful what you write down...
                                                                        </p>
                                                                    </div>

                                                                    <div class="mt-6 sm:mt-5">
                                                                        <div
                                                                            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                                                                        >
                                                                            <label
                                                                                for="country"
                                                                                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                                                                            >
                                                                                Difficulty
                                                                            </label>
                                                                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                                                                <div
                                                                                    class="max-w-lg rounded-md shadow-sm sm:max-w-xs"
                                                                                >
                                                                                    <select
                                                                                        id="country"
                                                                                        class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                                                                        v-model="
                                                                                            picture.form.difficulty
                                                                                        "
                                                                                    >
                                                                                        <option value="1"
                                                                                            >1 (Easy)</option
                                                                                        >
                                                                                        <option value="2"
                                                                                            >2 (Normal)</option
                                                                                        >
                                                                                        <option value="3"
                                                                                            >3 (Medium)</option
                                                                                        >
                                                                                        <option value="4"
                                                                                            >4 (A bit unfair)</option
                                                                                        >
                                                                                        <option value="5"
                                                                                            >5 (Hard)</option
                                                                                        >
                                                                                    </select>
                                                                                </div>
                                                                                <p
                                                                                    class="mt-2 text-xs text-red-600"
                                                                                    v-show="
                                                                                        $page.errors[
                                                                                            'picture.difficulty'
                                                                                        ]
                                                                                    "
                                                                                    v-for="(error, index) in $page
                                                                                        .errors['picture.difficulty']"
                                                                                    :key="index"
                                                                                >
                                                                                    {{ error }}
                                                                                </p>
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                                                                        >
                                                                            <label
                                                                                for="country"
                                                                                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                                                                            >
                                                                                Active
                                                                            </label>
                                                                            <div class="mt-4 sm:mt-0 sm:col-span-2">
                                                                                <div class="max-w-lg">
                                                                                    <div class="mt-4">
                                                                                        <div
                                                                                            class="relative flex items-start"
                                                                                        >
                                                                                            <div
                                                                                                class="flex items-center h-5"
                                                                                            >
                                                                                                <input
                                                                                                    id="offers"
                                                                                                    type="checkbox"
                                                                                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                                                                                    v-model="
                                                                                                        picture.form
                                                                                                            .active
                                                                                                    "
                                                                                                />
                                                                                            </div>
                                                                                            <div
                                                                                                class="ml-3 text-sm leading-5"
                                                                                            >
                                                                                                <p
                                                                                                    class="text-gray-500"
                                                                                                >
                                                                                                    Make the picture
                                                                                                    active to be
                                                                                                    included in the quiz
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <p
                                                                                            class="mt-2 text-xs text-red-600"
                                                                                            v-show="
                                                                                                $page.errors[
                                                                                                    'picture.active'
                                                                                                ]
                                                                                            "
                                                                                            v-for="(error,
                                                                                            index) in $page.errors[
                                                                                                'picture.active'
                                                                                            ]"
                                                                                            :key="index"
                                                                                        >
                                                                                            {{ error }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                                                                        >
                                                                            <label
                                                                                for="country"
                                                                                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                                                                            >
                                                                                Picture
                                                                            </label>
                                                                            <div class="mt-4 sm:mt-0 sm:col-span-2">
                                                                                <div class="flex items-center flex-col">
                                                                                    <div
                                                                                        class="w-full"
                                                                                        v-if="picture.form.id == null"
                                                                                    >
                                                                                        <div class="w-full">
                                                                                            <input
                                                                                                @change="
                                                                                                    getFileFromPictureInput
                                                                                                "
                                                                                                type="file"
                                                                                                ref="picture"
                                                                                                style="display: none;"
                                                                                            />
                                                                                            <span
                                                                                                class="rounded-md shadow-sm"
                                                                                            >
                                                                                                <button
                                                                                                    @click="
                                                                                                        $refs.picture.click()
                                                                                                    "
                                                                                                    type="button"
                                                                                                    :class="{
                                                                                                        'border-red-500 text-red-700 placeholder-red-300 focus:border-red-500 focus:shadow-outline-red':
                                                                                                            $page.errors
                                                                                                                .image,
                                                                                                    }"
                                                                                                    class="py-2 px-3 border border-gray-300 rounded-md text-sm leading-4 font-medium text-gray-600 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                                                                                                >
                                                                                                    Browse
                                                                                                </button>
                                                                                            </span>
                                                                                        </div>

                                                                                        <div class="mt-4 w-full">
                                                                                            <p
                                                                                                class="mt-2 text-xs text-red-600"
                                                                                                v-show="
                                                                                                    $page.errors[
                                                                                                        'picture.image'
                                                                                                    ]
                                                                                                "
                                                                                                v-for="(error,
                                                                                                index) in $page.errors[
                                                                                                    'picture.image'
                                                                                                ]"
                                                                                                :key="index"
                                                                                            >
                                                                                                {{ error }}
                                                                                            </p>
                                                                                            <div
                                                                                                v-if="
                                                                                                    picture.form.image
                                                                                                "
                                                                                                :class="{
                                                                                                    'border-red-500 text-red-900 placeholder-red-300':
                                                                                                        $page.errors
                                                                                                            .image,
                                                                                                    'border-indigo-500': !$page
                                                                                                        .errors.image,
                                                                                                }"
                                                                                                class="relative text-gray-500 text-xs border rounded-md w-auto box-border p-4"
                                                                                            >
                                                                                                <div
                                                                                                    class="hidden sm:block absolute top-0 right-0 pt-4 pr-4"
                                                                                                >
                                                                                                    <button
                                                                                                        @click="
                                                                                                            picture.form.image = null
                                                                                                        "
                                                                                                        type="button"
                                                                                                        class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150"
                                                                                                    >
                                                                                                        <svg
                                                                                                            class="h-6 w-6"
                                                                                                            stroke="currentColor"
                                                                                                            fill="none"
                                                                                                            viewBox="0 0 24 24"
                                                                                                        >
                                                                                                            <path
                                                                                                                stroke-linecap="round"
                                                                                                                stroke-linejoin="round"
                                                                                                                stroke-width="2"
                                                                                                                d="M6 18L18 6M6 6l12 12"
                                                                                                            />
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </div>
                                                                                                File to upload :
                                                                                                {{
                                                                                                    picture.form.image
                                                                                                        .name
                                                                                                }}<br />
                                                                                                Type :
                                                                                                {{
                                                                                                    picture.form.image
                                                                                                        .type
                                                                                                }}<br />
                                                                                                Size :
                                                                                                {{
                                                                                                    readableBytes(
                                                                                                        picture.form
                                                                                                            .image.size
                                                                                                    )
                                                                                                }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="w-full" v-else>
                                                                                        <div class="w-full">
                                                                                            <div class="px-2 py-2">
                                                                                                <a
                                                                                                    class="flex justify-center self-center"
                                                                                                    :href="
                                                                                                        picture.form
                                                                                                            .image
                                                                                                    "
                                                                                                    target="_blank"
                                                                                                >
                                                                                                    <img
                                                                                                        class="rounded-lg w-auto"
                                                                                                        :src="
                                                                                                            picture.form
                                                                                                                .image
                                                                                                        "
                                                                                                        :alt="
                                                                                                            picture.form
                                                                                                                .id
                                                                                                        "
                                                                                                    />
                                                                                                </a>
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
                                                </div>
                                            </div>
                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                                    <button
                                                        @click="submitPicture"
                                                        type="button"
                                                        class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-indigo-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                                    >
                                                        {{ picture.form.id !== null ? 'Update' : 'Create' }}
                                                    </button>
                                                </span>
                                                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                                                    <button
                                                        @click="pictureModal('close')"
                                                        type="button"
                                                        class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                                    >
                                                        Go back
                                                    </button>
                                                </span>
                                                <span
                                                    class="mt-3 mr-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto"
                                                    v-if="picture.form.id != null"
                                                >
                                                    <button
                                                        @click="deletePicture(picture.form.id)"
                                                        type="button"
                                                        class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                                    >
                                                        Delete
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /End picture modal -->
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

              <div class="mt-6 sm:mt-5 mb-5">
                <div
                    class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                  <label
                      for="callout"
                      class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                  >
                    Similar Questions
                  </label>

                  <similar-questions :map-id="question.map.id" :question-id="question.id" :similar-questions-ids="question.similar_question_ids" :questions="question.map.questions"></similar-questions>

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

                    <div>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <div class="relative flex-grow focus-within:z-10">
                                <input
                                    v-on:keyup.enter="submitFakeAnswer"
                                    v-model="fakeAnswer.form.callout"
                                    :disabled="loading"
                                    id="email"
                                    class="form-input block w-full rounded-none rounded-l-md transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                />
                            </div>
                            <button
                                @click="submitFakeAnswer"
                                class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-r-md text-gray-700 bg-gray-50 hover:text-gray-500 hover:bg-white focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                            >
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </button>
                        </div>

                        <p
                            class="mt-2 text-xs text-red-600"
                            v-show="$page.errors['fake-answer.callout']"
                            v-for="(error, index) in $page.errors['fake-answer.callout']"
                            :key="index"
                        >
                            {{ error }}
                        </p>

                        <div>
                            <ul class="mt-3 grid grid-rows-3 grid-flow-col gap-4">
                                <li
                                    v-for="fakeAnswer in question.fakeAnswers"
                                    class="col-span-1 flex shadow-sm rounded-md"
                                >
                                    <div
                                        class="flex-1 flex items-center justify-between border-t border-r border-b border-gray-200 bg-white rounded-r-md"
                                    >
                                        <div class="flex-1 px-4 py-2 text-sm leading-5 truncate">
                                            <a
                                                href="#"
                                                class="text-gray-900 font-medium hover:text-gray-600 transition ease-in-out duration-150"
                                                >{{ fakeAnswer.callout }}</a
                                            >
                                        </div>
                                        <div class="flex-shrink-0 pr-2">
                                            <button
                                                :disabled="loading"
                                                @click="deleteFakeAnswer(fakeAnswer.id)"
                                                class="w-8 h-8 inline-flex items-center justify-center text-gray-400 rounded-full bg-transparent hover:text-gray-500 focus:outline-none focus:text-gray-500 focus:bg-gray-100 transition ease-in-out duration-150"
                                            >
                                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"
                                                    ></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
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
import SimilarQuestions from './Components/Similar-Questions';
import Layout from '../Shared/Layout';

export default {
    components: {
        Layout,
        SimilarQuestions,
    }, // End Components
    props: {
        question: Object,
    }, // End Props
    remember: 'form',
    data() {
        return {
            form: {
                callout: this.question.callout,
                published: this.question.published,
                template: null,
            },
            loading: false,

            // Everything that has to do with creating a new image for the question
            picture: {
                form: {
                    id: null,
                    difficulty: '1',
                    active: 0,
                    image: null,
                },
                modal: false,
            },

            // Everything that has to do with creating a new fake answer for the question
            fakeAnswer: {
                form: {
                    callout: '',
                },
            },
        };
    }, // End Data
    methods: {
        updateQuestion() {
            this.loading = true;

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
                    this.loading = false;
                    this.form.template = null;
                });
        }, // End updateQuestion()

        getFileFromInput(event) {
            if (typeof event.target.files[0] !== 'undefined') {
                this.form.template = event.target.files[0];
            }
        }, //End getFileFromInput()

        getFileFromPictureInput(event) {
            if (typeof event.target.files[0] !== 'undefined') {
                this.picture.form.image = event.target.files[0];
            }
        }, //End getFileFromPictureInput()

        readableBytes(bytes) {
            var i = Math.floor(Math.log(bytes) / Math.log(1024)),
                sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return (bytes / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
        }, // End readableBytes()

        pictureModal(state, data = null) {
            if (state == 'close') {
                this.picture.modal = false;
                return;
            }

            this.picture.modal = true;

            if (data !== null) {
                this.picture.form.id = data.id;
                this.picture.form.difficulty = data.difficulty;
                this.picture.form.active = data.active;
                this.picture.form.image = data.image.location;
            }
        }, // End pictureModal()

        submitPicture() {
            this.loading = true;

            var data = new FormData();

            let submitRoute = '';

            if (this.picture.form.id != null) {
                submitRoute = route('question.update.picture', {
                    map: this.question.map.id,
                    question: this.question.id,
                    picture: this.picture.form.id,
                });
                data.append('_method', 'PATCH');
            } else {
                submitRoute = route('question.store.picture', {
                    map: this.question.map.id,
                    question: this.question.id,
                });
                data.append('_method', 'POST');
            }

            for (const [field, value] of Object.entries(this.picture.form)) {
                if (field == 'id' && this.picture.form.id == null) {
                    continue;
                }
                if (field == 'image' && this.picture.form.id != null) {
                    continue;
                }
                data.append('picture[' + field + ']', this.picture.form[field]);
            }

            this.$inertia.post(submitRoute, data).then(() => {
                this.loading = false;

                // Check if any errors exist
                if (Object.keys(this.$page.errors).length === 0) {
                    this.resetPicture();
                }
            });
        }, // End submitPicture()

        deletePicture(id) {
            if (!confirm('Are you sure you want to delete this picture from the question?')) {
                return;
            }

            this.loading = true;

            var data = new FormData();

            data.append('_method', 'DELETE');

            this.$inertia
                .post(
                    route('question.destroy.picture', {
                        map: this.question.map.id,
                        question: this.question.id,
                        picture: id,
                    }),
                    data
                )
                .then(() => {
                    this.loading = false;
                    this.resetPicture();
                });
        }, // End deletePicture()

        resetPicture() {
            this.loading = false;
            this.picture.form.difficulty = '1';
            this.picture.form.active = 0;
            this.picture.form.image = null;
            this.picture.form.id = null;
            this.pictureModal('close');

            var errors = this.$page.errors;
            // will only delete the error keys from picture
            Object.keys(errors).forEach((key) => {
                if (key.match('picture')) {
                    delete this.$page.errors[key];
                }
            });
        }, // End resetPicture()

        submitFakeAnswer() {
            this.loading = true;

            var data = new FormData();

            data.append('_method', 'POST');

            for (const [field, value] of Object.entries(this.fakeAnswer.form)) {
                data.append('fake-answer[' + field + ']', this.fakeAnswer.form[field]);
            }

            this.$inertia
                .post(
                    route('question.store.fake-answer', { map: this.question.map.id, question: this.question.id }),
                    data
                )
                .then(() => {
                    this.loading = false;

                    // Check if any errors exist
                    if (Object.keys(this.$page.errors).length === 0) {
                        this.resetFakeAnswer();
                    }
                });
        }, // End submitFakeAnswer()

        deleteFakeAnswer(id) {
            if (!confirm('Are you sure you want to delete this fake answer from the question?')) {
                return;
            }

            this.loading = true;

            var data = new FormData();

            data.append('_method', 'DELETE');

            this.$inertia
                .post(
                    route('question.destroy.fake-answer', {
                        map: this.question.map.id,
                        question: this.question.id,
                        fakeAnswer: id,
                    }),
                    data
                )
                .then(() => {
                    this.loading = false;
                    this.resetFakeAnswer();
                });
        }, // End deletePicture()

        resetFakeAnswer() {
            this.loading = false;
            this.fakeAnswer.form.callout = '';

            var errors = this.$page.errors;
            // will only delete the error keys from picture
            Object.keys(errors).forEach((key) => {
                if (key.match('fake-answer')) {
                    delete this.$page.errors[key];
                }
            });
        }, // End resetFakeAnswer()
    }, // End Methods
    watch: {
        'picture.modal': function (newVal, oldVal) {
            if (newVal == false) {
                this.resetPicture();
            }
        },
        'form.published'(newVal) {
            if (newVal === true) {
                this.form.published = 1;
            } else if (newVal === false) {
                this.form.published = 0;
            }
        },
        'picture.form.active'(newVal) {
            if (newVal === true) {
                this.picture.form.active = 1;
            } else if (newVal === false) {
                this.picture.form.active = 0;
            }
        },
    }, // End Watch
};
</script>
