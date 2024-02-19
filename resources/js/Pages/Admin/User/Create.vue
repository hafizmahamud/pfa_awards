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

const props = defineProps({
    jurisdictions: Object
});

const form = useForm({
    _method: 'POST',
    name: '',
    email: '',
    telephone: '',
    mobile: '',
    jurisdiction: '',
    password: '',
    role: '',
    isSendEmail : false,
    //rolename: props.roles.name,
    //photo: null,
});

const submit = () => {
    form.post(route("user.store"));
};

const generatePassword = (e) => {
    e.preventDefault();
    let CharacterSet = ''
    let password = ''
    let size = 12
    
    CharacterSet += 'abcdefghijklmnopqrstuvwxyz'
    CharacterSet += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    CharacterSet += '0123456789'
    CharacterSet += '![]{}()%&*$#^<>~@|'
    
    for (let i = 0; i < size; i++) {
      password += CharacterSet.charAt(Math.floor(Math.random() * CharacterSet.length))
    }

    console.log('password: ' + password)
    
    form.password = password
};

</script>

<template>
    <AppLayout title="Create User">
        <template #header>
            <div class="col-sm-8 my-5">
                <h2>Create User</h2>
            </div>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- <Welcome /> -->
                    <div class="main">
                        <div class="container pb-5">
                            <div class="row g-0">
                                <!-- New User Form -->
                                <form @submit.prevent="submit">
                                    <div class="details">
                                        <InputLabel for="name" value="Name" class="form-label" />
                                        <TextInput id="name" v-model="form.name" type="text" class="form-control"
                                            autocomplete="name" />
                                       <div v-if="form.errors.name" v-text="form.errors.name" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="details">
                                        <InputLabel for="email" value="Email" class="form-label" />
                                        <TextInput id="email" v-model="form.email" type="email" class="form-control" />
                                        <div v-if="form.errors.email" v-text="form.errors.email" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="details">
                                        <InputLabel for="telephone" value="Telephone" class="form-label" />
                                        <TextInput id="telephone" v-model="form.telephone" type="text"
                                            class="form-control" autocomplete="telephone" />
                                       <div v-if="form.errors.telephone" v-text="form.errors.telephone" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="details">
                                        <InputLabel for="mobile" value="Mobile" class="form-label" />
                                        <TextInput id="mobile" v-model="form.mobile" type="text" class="form-control"
                                            autocomplete="mobile" />
                                        <div v-if="form.errors.mobile" v-text="form.errors.mobile" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <!--<div class="details ">
                                        <InputLabel for="jurisdiction" value="Jurisdiction" class="form-label" />
                                        <TextInput
                                            id="jurisdiction"
                                            v-model="form.jurisdiction"
                                            type="text"
                                            class="form-control"
                                            autocomplete="jurisdiction"
                                        />
                                        <InputError :message="form.errors.jurisdiction" class="mt-2" />
                                    </div>-->
                                    <div class="details">
                                        <label class="me-3" for="tag">Jurisdiction</label>
                                            <select class="form-select form-inline mw-s" aria-label=".form-select-sm example"
                                                v-model="form.jurisdiction">
                                                <option value="" selected>Select Jurisdiction</option>
                                                <option v-for="jurisdiction in props.jurisdictions" :key="jurisdiction.id"
                                                    :value="jurisdiction.name">
                                                    {{ jurisdiction.name }}
                                                </option>
                                            </select>
                                            <div v-if="form.errors.jurisdiction" v-text="form.errors.jurisdiction" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <!-- <div class="details">
                                       <InputLabel for="role" value="Role" class="form-label" />

                                        <select id="role" name="role" class="form-control" v-model="form.role">
                                            <option value="Admin">Admin</option>
                                            <option value="Researcher">Researcher</option>
                                            <option value="Industrial Officer">Industrial Officer</option>
                                        </select>
                                    </div> -->
                                    <div class="details">
                                        <label class="me-3" for="tag">Role</label>
                                        <select id="role" name="role" class="form-select form-inline mw-s2"
                                            aria-label=".form-select-sm example" v-model="form.role">
                                            <option value="" selected>Select Role</option>
                                            <option value="Admin">Admin</option>
                                            <option value="Industrial">Industrial Officer</option>
                                            <option value="Researcher">Researcher</option>
                                        </select>
                                        <div v-if="form.errors.role" v-text="form.errors.role" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div style="display: flex; flex-direction: row;">
                                        <div style="width: 90%;">
                                            <InputLabel for="password" value="Password" class="form-label" />
                                            <TextInput id="password" v-model="form.password" type="password"
                                                class="form-control" autocomplete="jurisdiction" />
                                            <div v-if="form.errors.password" v-text="form.errors.password" class="text-danger text-xs mt-1"></div>
                                        </div>
                                        
                                        <div style="margin-top: 25px;">
                                            <button class="btn btn-primary" style="margin-left: 10px; height: 35px; padding-top: 3px" @click="generatePassword">Generate Password</button>
                                        </div>
                                    </div>
                                    
                                    <div class="details" style="margin-top: 20px;">
                                        <input id="sendEmail" type="checkbox" v-model="form.isSendEmail">
                                        <InputLabel for="sendEmail" value="Send access details via email" class="form-label" style="margin-left: 10px;"/>
                                    </div>


                                    <div class="p-3">
                                        <button class="{ 'opacity-25': form.processing } .btn btn-primary "
                                            :disabled="form.processing" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
