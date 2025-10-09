<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    projects: Array,
    categories: Array,
});

const form = useForm({
    category_id: '',
    amount: '',
    description: '',
    reason: '',
    expected_date: '',
    priority: 'normal',
    project_id: '',
});

const submit = () => {
    form.post(route('payment-requests.store'));
};
</script>

<template>
    <Head title="Tạo phiếu đề xuất" />

    <AdminLayout>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tạo phiếu đề xuất</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <form @submit.prevent="submit">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Danh mục <span class="text-danger">*</span></label>
                                        <select v-model="form.category_id" class="form-control" :class="{ 'is-invalid': form.errors.category_id }">
                                            <option value="">-- Chọn danh mục --</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <div v-if="form.errors.category_id" class="invalid-feedback">{{ form.errors.category_id }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Số tiền (VNĐ) <span class="text-danger">*</span></label>
                                        <input v-model="form.amount" type="number" class="form-control" :class="{ 'is-invalid': form.errors.amount }" placeholder="Nhập số tiền">
                                        <div v-if="form.errors.amount" class="invalid-feedback">{{ form.errors.amount }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Mô tả <span class="text-danger">*</span></label>
                                <textarea v-model="form.description" class="form-control" :class="{ 'is-invalid': form.errors.description }" rows="3" placeholder="Mô tả chi tiết"></textarea>
                                <div v-if="form.errors.description" class="invalid-feedback">{{ form.errors.description }}</div>
                            </div>

                            <div class="form-group">
                                <label>Lý do <span class="text-danger">*</span></label>
                                <textarea v-model="form.reason" class="form-control" :class="{ 'is-invalid': form.errors.reason }" rows="2" placeholder="Lý do chi"></textarea>
                                <div v-if="form.errors.reason" class="invalid-feedback">{{ form.errors.reason }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Ngày dự kiến <span class="text-danger">*</span></label>
                                        <input v-model="form.expected_date" type="date" class="form-control" :class="{ 'is-invalid': form.errors.expected_date }">
                                        <div v-if="form.errors.expected_date" class="invalid-feedback">{{ form.errors.expected_date }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Ưu tiên <span class="text-danger">*</span></label>
                                        <select v-model="form.priority" class="form-control" :class="{ 'is-invalid': form.errors.priority }">
                                            <option value="normal">Bình thường</option>
                                            <option value="urgent">Gấp</option>
                                        </select>
                                        <div v-if="form.errors.priority" class="invalid-feedback">{{ form.errors.priority }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Dự án (nếu có)</label>
                                        <select v-model="form.project_id" class="form-control" :class="{ 'is-invalid': form.errors.project_id }">
                                            <option value="">-- Chọn dự án --</option>
                                            <option v-for="project in projects" :key="project.id" :value="project.id">
                                                {{ project.name }} ({{ project.code }})
                                            </option>
                                        </select>
                                        <div v-if="form.errors.project_id" class="invalid-feedback">{{ form.errors.project_id }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <i class="fas fa-save"></i> Lưu phiếu
                            </button>
                            <a :href="route('payment-requests.index')" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
