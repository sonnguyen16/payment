import { router } from '@inertiajs/vue3';

export function usePaymentRequest() {
    const submitForApproval = (requestId) => {
        if (confirm('Xác nhận gửi duyệt? Sau khi gửi, bạn không thể chỉnh sửa phiếu này')) {
            router.post(route('payment-requests.submit', requestId), {}, {
                onSuccess: () => {
                    // Success message handled by backend
                },
            });
        }
    };

    const cancelRequest = (requestId) => {
        const reason = prompt('Nhập lý do hủy phiếu:');
        if (reason) {
            router.post(route('payment-requests.cancel', requestId), { reason }, {
                onSuccess: () => {
                    // Success message handled by backend
                },
            });
        }
    };

    const approveRequest = (requestId) => {
        if (confirm('Xác nhận phê duyệt phiếu này?')) {
            const note = prompt('Ghi chú (không bắt buộc):');
            router.post(route('approvals.approve', requestId), { note }, {
                onSuccess: () => {
                    // Success message handled by backend
                },
            });
        }
    };

    const rejectRequest = (requestId) => {
        const reason = prompt('Nhập lý do từ chối:');
        if (reason) {
            router.post(route('approvals.reject', requestId), { reason }, {
                onSuccess: () => {
                    // Success message handled by backend
                },
            });
        }
    };

    const deleteRequest = (requestId) => {
        if (confirm('Xác nhận xóa phiếu này? Hành động này không thể hoàn tác!')) {
            router.delete(route('payment-requests.destroy', requestId), {
                onSuccess: () => {
                    // Success message handled by backend
                },
            });
        }
    };

    const getStatusBadgeClass = (status) => {
        const classes = {
            'draft': 'badge-secondary',
            'pending_department_head': 'badge-warning',
            'pending_accountant': 'badge-info',
            'pending_ceo': 'badge-primary',
            'pending_payment': 'badge-success',
            'paid': 'badge-success',
            'rejected': 'badge-danger',
            'cancelled': 'badge-dark',
            'deleted': 'badge-dark',
        };
        return classes[status] || 'badge-secondary';
    };

    const getPriorityBadgeClass = (priority) => {
        return priority === 'urgent' ? 'badge-danger' : 'badge-secondary';
    };

    return {
        submitForApproval,
        cancelRequest,
        approveRequest,
        rejectRequest,
        deleteRequest,
        getStatusBadgeClass,
        getPriorityBadgeClass,
    };
}
