<script setup>
import AppLayoutVue from '../../Layouts/AppLayout.vue';
import AddOnHeader from '../../Components/AddOnHeader.vue';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    conditionCategories: {
        type: Object,
        default: () => ({}),
    }
});

// console.log(props.conditionCategories);

let form = useForm({
    id: '',
    name: '',
    condition_category_id: '',
    type: '',
    file: ''
});

let submit_button = ref('Add');
let hasChild = ref(false);

let edit_form = (item) => {
    // console.log(item);
    form.id = item.id;
    form.name = item.name;
    form.condition_category_id = item.category_id;
    form.type = item.type;
    submit_button = 'Update';
    hasChild = item.hasChild;
}

let delete_item = (id, type) => {
    // console.log(item);
    if (type == 'category') {
        if (confirm('This item might have child items, deleting it will delete the child items too. Proceed?')) {
            Inertia.post(route('admin.delete'), {
                'id': id,
                'type': type
            });
        }
    } else {
        Inertia.post(route('admin.delete'), {
            'id': id,
            'type': type
        });
    }

}

let submit = () => {
    form.post('/admin/categories', {
        preserveScroll: true,
        onSuccess: () => reset_form()
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
    <AppLayoutVue title="Add or Edit Categories">
        <template #header>
            <AddOnHeader prefix="admin" title="Add or Edit Categories" selected="admin.categories" @jump-to="jumpTo">
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
                                        <template v-for="category in conditionCategories" :key="category.id">
                                            <tr>
                                                <td>{{ category.updated_at }}</td>
                                                <td>{{ category.name }}</td>
                                                <td>
                                                    <button id="edit" class="btn-none"
                                                        @click.prevent="edit_form({ id: category.id, name: category.name, category_id: '', type: 'category', hasChild: category.condition_subcategories != [] })">
                                                        Edit </button>
                                                    <Link Link id="remove" class="btn-none"
                                                        @click="delete_item(category.id, 'category')">
                                                    Remove</Link>
                                                </td>
                                            </tr>
                                            <template v-if="category.condition_subcategories">
                                                <tr v-for="subcategory in category.condition_subcategories"
                                                    class="subcategory" :key="subcategory.id">
                                                    <td>{{ subcategory.updated_at }}</td>
                                                    <td style="padding-left: 30px !important;">- {{ subcategory.name }}
                                                    </td>
                                                    <td>
                                                        <button id="edit" class="btn-none"
                                                            @click.prevent="edit_form({ id: subcategory.id, name: subcategory.name, category_id: category.id, type: 'subcategory', hasChild: subcategory.condition_topics != [] })">
                                                            Edit</button>
                                                        <Link id="remove" class="btn-none"
                                                            @click="delete_item(subcategory.id, 'subcategory')">
                                                        Remove</Link>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-4">
                                <button id="new-post" class="btn-primary" @click="reset_form()">
                                    New category / subcategory</button>
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
                                    <div v-if="form.condition_category_id == ''" class="row g-0">
                                        <div class="col-12 col-md-2 my-3">
                                            <label for="name" class="form-label">Image:</label>
                                        </div>
                                        <div class="col-12 col-md-10 my-3">
                                            <input type="file" @input="form.file = $event.target.files[0]" />
                                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                                {{ form.progress.percentage }}%
                                            </progress>
                                            <div v-if="form.errors.file" v-text="form.errors.file"
                                                class="text-danger text-xs mt-1">
                                            </div>
                                        </div>
                                        <!-- {{ form.file }} -->
                                    </div>
                                    <div class="row g-0">
                                        <div class="col-12 col-md-2 my-3">
                                            <p>Parent:</p>
                                        </div>
                                        <select v-model="form.condition_category_id"
                                            class="form-select form-select-sm form-inline" aria-label=".form-select-sm">
                                            <option selected value=""
                                                :disabled="hasChild && form.type == 'subcategory'">Select category
                                            </option>
                                            <option v-for="category in conditionCategories" :value="category.id"
                                                :disabled="hasChild && form.type == 'category'">{{
                                                        category.name
                                                }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="row g-0">
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" id="publish" class="btn-primary"
                                                :disabled="form.processing">{{ submit_button
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
