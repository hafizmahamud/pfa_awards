<script setup>
import AppLayoutVue from '../../Layouts/AppLayout.vue';
import AddOnHeader from '../../Components/AddOnHeader.vue';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    jurisdictions: {
        type: Object,
        default: () => ({}),
    }
});

// console.log(props.conditionCategories);

let form = useForm({
    id: '',
    name: '',
    logo: ''
});

let submit_button = ref('Add');

let edit_form = (item) => {
    form.id = item.id;
    form.name = item.name;
    form.logo = '';
    submit_button = 'Update';
}

let delete_item = (id, type) => {
    // if (type == 'clauses') {
    // console.log(id);
    // console.log(type);
    if (confirm('This item might have child items, deleting it will delete the child items too. Proceed?')) {
        Inertia.post(route('admin.delete'), {
            'id': id,
            'type': type
        });
    }

}

let submit = () => {
    form.post('/admin/jurisdictions', {
        // preserveScroll: true,
        onSuccess: () => {
            form.reset();
            submit_button = 'Add';
        }
    });
}

let reset_form = () => {
    form.reset();
    hasChild = false;
    submit_button = 'Add';
}

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}
</script>

<template>
    <AppLayoutVue title="Add or Edit Jurisdictions">
        <template #header>
            <AddOnHeader prefix="admin" title="Add or Edit Jurisdictions" selected="admin.jurisdictions"
                @jump-to="jumpTo">
            </AddOnHeader>
        </template>
        <div class="mt-5 mb-5 pb-5">
            <div class="app-cont">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-7 mb-4">
                            <div class="table-responsive  add-edit border border-color-secondary">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Edited</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="jurisdiction in jurisdictions" :key="jurisdiction.id">
                                            <tr>
                                                <td>{{ jurisdiction.updated_at }}</td>
                                                <td>{{ jurisdiction.name }}</td>
                                                <td>
                                                    <button id="edit" class="btn-none"
                                                        @click.prevent="edit_form({ id: jurisdiction.id, name: jurisdiction.name })">
                                                        Edit </button>
                                                    <Link Link id="remove" class="btn-none"
                                                        @click="delete_item(jurisdiction.id, 'jurisdiction')">
                                                    Remove</Link>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-4">
                                <button id="new-post" class="btn-primary" @click="reset_form()">
                                    New jurisdiction</button>
                            </div>
                        </div>
                        <div class="col-12 col-md-5 mb-4">
                            <form @submit.prevent="submit">
                                <div class="pt-3">
                                    <div class="row g-0">
                                        <div class="col-12 col-md-2 my-3">
                                            <label for="name" class="form-label">Name:</label>
                                        </div>
                                        <div class="col-12 col-md-10 my-3">
                                            <input v-model="form.name" type="name" class="form-control" id="title">
                                            <div v-if="form.errors.name" v-text="form.errors.name"
                                                class="text-danger text-xs mt-1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-0">
                                        <div class="col-12 col-md-2 my-3">
                                            <label for="logo" class="form-label">Logo:</label>
                                        </div>
                                        <div class="col-12 col-md-10 my-3">
                                            <input type="file" @input="form.logo = $event.target.files[0]"
                                                accept="image/x-png,image/gif,image/jpeg" />
                                            <div v-if="form.errors.logo" v-text="form.errors.logo"
                                                class="text-danger text-xs mt-1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-0">
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" id="publish" class="btn-primary"
                                                :disabled="form.processing">{{
    submit_button
                                                }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayoutVue>
</template>
