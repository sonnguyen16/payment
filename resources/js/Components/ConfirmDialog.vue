<script setup>
import { ref } from 'vue';

const emit = defineEmits(['confirm', 'cancel']);

const props = defineProps({
    title: {
        type: String,
        default: 'Xác nhận',
    },
    message: {
        type: String,
        required: true,
    },
    confirmText: {
        type: String,
        default: 'Xác nhận',
    },
    cancelText: {
        type: String,
        default: 'Hủy',
    },
    type: {
        type: String,
        default: 'warning', // success, info, warning, danger
    },
});

const show = ref(false);

const open = () => {
    show.value = true;
};

const close = () => {
    show.value = false;
};

const confirm = () => {
    emit('confirm');
    close();
};

const cancel = () => {
    emit('cancel');
    close();
};

defineExpose({ open, close });
</script>

<template>
    <div v-if="show" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" :class="`bg-${type}`">
                    <h5 class="modal-title text-white">{{ title }}</h5>
                    <button type="button" class="close text-white" @click="cancel">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ message }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cancel">
                        {{ cancelText }}
                    </button>
                    <button type="button" class="btn" :class="`btn-${type}`" @click="confirm">
                        {{ confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
