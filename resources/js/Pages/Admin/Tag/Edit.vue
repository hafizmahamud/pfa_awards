<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';


import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps({
  tags: {
    type: Object,
    default: () => ({}),
  },
  alltag: {
    type: Object,
    default: () => ({}),
  },
});


const form = useForm({
    _method: 'POST',
    name: props.tags.name,
    parenttag: props.tags.parent_id
});

const submit = () => {
    form.put(route("tag.update", props.tags.id));
};

</script>

<template>
    <AppLayout title="Edit Tag">
        <template #header>
            <div class="col-sm-8 my-5">
                <h2>Edit Tag </h2>
            </div>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- <Welcome /> -->
                    <div class="main">
                        <div class="container pb-5">
                            <div class="row g-0">
                                <!-- Edit User FORM Here -->
                                <form @submit.prevent="submit">
                                             <div class="details">
                                                <InputLabel for="name" value="Name" class="form-label" />                         
                                                    <TextInput
                                                        id="name"
                                                        v-model="form.name"
                                                        type="text"
                                                        class="form-control"
                                                        autocomplete="name"
                                                    />
                                                 <div v-if="form.errors.name" v-text="form.errors.name" class="text-danger text-xs mt-1"></div>
                                            </div>


                                            <div class="details p-3">
                                                    <!--<label class="me-3" for="tag">Parent Tag</label>
                                                        <select class="form-select form-inline mw-s" aria-label=".form-select-sm example"
                                                            v-model="form.parenttag">
                                                            <option value="" selected>Select Parent Tag</option>
                                                            <option v-for="tag in props.alltag" :key="tag.id"
                                                                :value="tag.id">
                                                                {{ tag.name }}
                                                            </option>
                                                        </select>-->
                                            </div>
                                            <button class="{ 'opacity-25': form.processing } .btn btn-primary " :disabled="form.processing" type="submit">Edit Tag</button>

                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>