<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div>
                <h2 class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-900">
                    Sign in to a account
                </h2>
                <p class="mt-2 text-center text-sm leading-5 text-gray-600 max-w">
                    This page should only be used for development purposes only.
                </p>
            </div>
            <form class="mt-8" action="#" method="POST">
                <input type="hidden" name="remember" value="true" />
                <div class="rounded-md shadow-sm">
                    <div>
                        <fieldset>
                            <div class="mt-1 rounded-md shadow-sm">
                                <div>
                                    <select
                                        v-model="form.user_id"
                                        aria-label="Country"
                                        class="form-select relative block w-full rounded-none rounded-md bg-1transparent focus:z-10 transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                    >
                                        <option v-for="user in users" :value="user.id">{{ user.username }}</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="mt-6">
                    <button
                        type="submit"
                        @click.prevent="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
                    >
                        <span class="absolute left-0 inset-y pl-3">
                            <svg
                                class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition ease-in-out duration-150"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </span>
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- <layout title="Login DEVELOPMENT">

    <div class="font-bold text-xl mb-8">Login</div>

    <div class="w-full">
      <div class="bg-primary shadow-md p-4 flex flex-col justify-between leading-normal border rounded" style="border-color: #172340;">
        
        <div class="grid grid-cols-12 gap-4">
          <form class="px-8 pt-6 pb-8 mb-4 col-span-4" enctype="multipart/form-data">
            <div class="mb-6">
              <label class="block text-sm font-medium mb-2" for="name">
                User
              </label>
              <select class="shadow rounded w-full py-2 px-3 mb-3 leading-tight focus:outline-none focus:shadow-outline" v-model="form.user_id">
                <option v-for="user in users" :value="user.id">{{ user.username }}</option>
              </select>
            </div>

            <div class="flex items-center">
              <button class="bg-secondary hover:bg-blue-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button" @click="submit">
                Submit
              </button>
            </div>
          </form>

        </div>

      </div>
    </div>

  </layout> -->
</template>

<script>
export default {
    props: {
        users: Array,
    }, // End props
    data() {
        return {
            form: {
                user_id: 1,
            },
        };
    }, // End data
    methods: {
        submit() {
            this.loading = true;

            var data = new FormData();

            data.append('user_id', this.form.user_id);

            data.append('_method', 'post');

            this.$inertia.post(route('login'), data).then(() => {
                // Maybe do something?
            });
        }, // End submit()
    }, // End methods
};
</script>
