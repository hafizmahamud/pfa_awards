<script setup>
import AppLayoutVue from '@/Layouts/AppLayout.vue';
import AddOnHeader from '@/Components/AddOnHeader.vue';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    clauses: {
        type: Object,
        default: () => ({}),
    }
});

// console.log(props.clauses);

let form = useForm({
    id: '',
    title: '',
    clause_id: '',
    type: '',
});

let submit_button = ref('Add');
let hasChild = ref(false);

let edit_form = (item) => {
    // console.log(item);
    form.id = item.id;
    form.title = item.title;
    form.clause_id = item.clause_id;
    form.type = item.type;
    submit_button = 'Update';
    hasChild = item.hasChild;
}

let delete_item = (id, type) => {
    console.log(id);
    console.log(type);
    // if (type == 'clauses') {
    if (confirm('This item might have child items, deleting it will delete the child items too. Proceed?')) {
        Inertia.post(route('admin.delete'), {
            'id': id,
            'type': type
        });
    }

}

let submit = () => {
    form.type = form.clause_id == "" ? 'clause' : 'subclause';
    form.post('/admin/clauses', {
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
    <AppLayoutVue title="Add or Edit Clauses">
        <template #header>
            <AddOnHeader prefix="admin" title="Add or Edit Clauses" selected="admin.clauses" @jump-to="jumpTo">
            </AddOnHeader>
        </template>
        <div class="mt-5 mb-5 pb-5">
            <div class="app-cont">
                <div class="container">
                    <div class="row">
                        <div class="col-md-7 col-12 mb-4">
                            <div class="table-responsive  add-edit border border-color-secondary">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Edited</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="clause in clauses" :key="clause.id">
                                            <tr>
                                                <td>{{ clause.updated_at }}</td>
                                                <td>{{ clause.title }}</td>
                                                <td>
                                                    <button id="edit" class="btn-none"
                                                        @click.prevent="edit_form({ id: clause.id, title: clause.title, clause_id: '', type: 'clause', hasChild: clause.subclauses != [] })">
                                                        Edit </button>
                                                    <Link Link id="remove" class="btn-none"
                                                        @click="delete_item(clause.id, 'clauses')">
                                                    Remove</Link>
                                                </td>
                                            </tr>
                                            <template v-if="clause.subclauses != []">
                                                <tr v-for="subclause in clause.subclauses" class="subclause"
                                                    :key="subclause.id">
                                                    <td>{{ subclause.updated_at }}</td>
                                                    <td style="padding-left: 30px !important;">- {{ subclause.title }}
                                                    </td>
                                                    <td>
                                                        <button id="edit" class="btn-none"
                                                            @click.prevent="edit_form({ id: subclause.id, title: subclause.title, clause_id: clause.id, type: 'subclause', hasChild: subclause.award_contents != [] })">
                                                            Edit</button>
                                                        <Link id="remove" class="btn-none"
                                                            @click="delete_item(subclause.id, 'subclauses')">
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
                                    New clause / subclause</button>
                            </div>
                        </div>
                        <div class="col-md-5 col-12 mb-4">
                            <form @submit.prevent="submit">
                                <div class="pt-3">
                                    <div class="row g-0">
                                        <div class="col-12 col-md-2 my-3">
                                            <label for="title" class="form-label">Title:</label>
                                        </div>
                                        <div class="col-12 col-md-10 my-3">
                                            <input v-model="form.title" type="title" class="form-control" id="title">
                                            <div v-if="form.errors.title" v-text="form.errors.title"
                                                class="text-danger text-xs mt-1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-0">
                                        <div class="col-12 col-md-2 my-3">
                                            <p>Parent:</p>
                                        </div>
                                        <select v-model="form.clause_id" class="form-select form-select-sm form-inline"
                                            aria-label=".form-select-sm">
                                            <option selected value="" :disabled="hasChild && form.type == 'subclause'">
                                                Select clause
                                            </option>
                                            <option v-for="clause in clauses" :value="clause.id"
                                                :disabled="hasChild && form.type == 'clause'">{{
                                                        clause.title
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
